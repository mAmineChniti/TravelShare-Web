<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Comments;
use App\Entity\PostImages;
use App\Repository\LikesRepository;
use App\Repository\PostsRepository;
use App\Repository\UsersRepository;
use App\Repository\CommentsRepository;
use App\Repository\PostImagesRepository;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ForumController extends AbstractController
{
    private function enrichPost(array $post, int $userId, CommentsRepository $commentsRepository, LikesRepository $likesRepository, PostImagesRepository $postImagesRepository): array
    {
        $post['comments'] = $commentsRepository->fetchById($post['postId']);
        $post['likesCount'] = $likesRepository->likesCounter($post['postId']);
        $post['dislikesCount'] = $likesRepository->dislikesCounter($post['postId']);
        $post['isLiked'] = $likesRepository->isLikedByUser($userId, $post['postId']);
        $images = $postImagesRepository->findImagesByPostId($post['postId']);
        if($images){
            $post['images'] = $images ? array_map(fn ($image) => base64_encode($image), $images) : [];
        } else {
            $post['images'] = [];
        }
        return $post;
    }

    #[Route('/forum', name: 'app_forum', methods: ['GET'])]
    public function index(
        Request $request,
        PostsRepository $postsRepository,
        CommentsRepository $commentsRepository,
        LikesRepository $likesRepository,
        PostImagesRepository $postImagesRepository,
    ): Response {
        $userId = 1;
        $offset = max(0, (int) $request->query->get('offset', 0));
        $limit = min(100, max(1, (int) $request->query->get('limit', 10)));

        $posts = $postsRepository->fetchPosts($offset, $limit, $userId);
        foreach ($posts as &$post) {
            $post = $this->enrichPost($post, $userId, $commentsRepository, $likesRepository, $postImagesRepository);
        }

        $postsToBeAnalyzed = $postsRepository->listAll();

        $postContents = array_map(function ($post) {
            return [
                'postId' => $post->getPostId(),
                'textContent' => $post->getTextContent(),
            ];
        }, $postsToBeAnalyzed);

        $geminiApiKey = $this->getParameter('app.gemini_api_key');

        if (!$geminiApiKey) {
            throw new \RuntimeException('API key is missing.');
        }

        $httpClient = HttpClient::create();
        $response = $httpClient->request('POST', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $geminiApiKey, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => json_encode([
                                    'posts' => $postContents,
                                    'instructions' => 'Analyze the text content of the posts and recommend 3 posts. Return only a JSON array with the postIds of the recommended posts.',
                                ]),
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $responseData = $response->toArray();

        $recommendedPostIds = [];
        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            $rawPostIds = $responseData['candidates'][0]['content']['parts'][0]['text'];

            $rawPostIds = preg_replace('/^```json\\n|\\n```$/', '', $rawPostIds);
            $recommendedPostIds = json_decode($rawPostIds, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                $recommendedPostIds = [];
            }
        }
        $recommendedPosts = $postsRepository->fetchPostsByIds($recommendedPostIds, $userId);
        if (!empty($recommendedPosts)) {
            foreach ($recommendedPosts as &$post) {
                $post = $this->enrichPost($post, $userId, $commentsRepository, $likesRepository, $postImagesRepository);
            }
        } else {
            $recommendedPosts = [];
        }

        $recommendedPostIds = array_column($recommendedPosts, 'postId');
        $posts = array_filter($posts, function ($post) use ($recommendedPostIds) {
            return !in_array($post['postId'], $recommendedPostIds);
        });

        return $this->render('forum/index.html.twig', [
            'posts' => $posts,
            'recommendedPosts' => $recommendedPosts,
        ]);
    }

    #[Route('/forum/post', name: 'app_forum_post', methods: ['POST'])]
    public function post(
        Request $request,
        PostsRepository $postsRepository,
        UsersRepository $userRepository,
        PostImagesRepository $postImagesRepository,
        ValidatorInterface $validator,
    ): Response {
        $postText = $request->request->get('postText');
        $postTitle = $request->request->get('postTitle');
        $userId = 1;

        $post = new Posts();
        $post->setTextContent($postText);
        $post->setPostTitle($postTitle);
        $post->setOwnerId($userId);
        $post->setCreatedAt(new \DateTime());
        $post->setUpdatedAt(new \DateTime());

        $errors = $validator->validate($post);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['error' => $errorMessages], 400);
        }

        $postsRepository->add($post);

        $uploadedFiles = $request->files->get('postImages');
        if ($uploadedFiles) {
            foreach ($uploadedFiles as $uploadedFile) {
                try {
                    $imageData = file_get_contents($uploadedFile->getPathname());
                    $postImage = new PostImages();
                    $postImage->setPost($post);
                    $postImage->setImage($imageData);
                    $postImagesRepository->add($postImage);
                } catch (FileException) {
                    return new JsonResponse(['error' => 'Failed to process uploaded image.'], 400);
                }
            }
        }
        $user = $userRepository->find($userId);
        $images = $postImagesRepository->findImagesByPostId($post->getPostId());
        $postData = [
            'postId' => $post->getPostId(),
            'name' => $user->getName(),
            'lastName' => $user->getLastName(),
            'textContent' => $post->getTextContent(),
            'postTitle' => $post->getPostTitle(),
            'images' => $images ? array_map(fn($image) => base64_encode($image), $images) : [],
            'isLiked' => false,
            'likesCount' => 0,
            'comments' => [],
        ];

        return $this->render('components/Post.html.twig', [
            'post' => $postData,
        ]);
    }

    #[Route('/forum/edit/{id}', name: 'app_forum_edit', methods: ['POST'])]
    public function edit(
        Request $request,
        PostsRepository $postsRepository,
        ValidatorInterface $validator,
        int $id,
    ): Response {
        $post = $postsRepository->find($id);

        if (!$post) {
            return new JsonResponse(['error' => 'Post not found.'], 404);
        }

        if (1 !== $post->getOwnerId()) {
            return new JsonResponse(['error' => 'Unauthorized.'], 403);
        }

        $postText = $request->request->get('editTextarea-' . $id);
        $post->setTextContent($postText);
        $post->setUpdatedAt(new \DateTime());

        $postTitle = $request->request->get('editTitle-' . $id);
        $post->setPostTitle($postTitle);

        $errors = $validator->validate($post);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['error' => $errorMessages], 400);
        }

        $postsRepository->update($post);

        return $this->render('components/PostText.html.twig', [
            'postId' => $post->getPostId(),
            'postTitle' => $post->getPostTitle(),
            'textContent' => $post->getTextContent(),
        ]);
    }

    #[Route('/forum/delete/{id}', name: 'app_forum_delete', methods: ['POST'])]
    public function delete(
        PostsRepository $postsRepository,
        int $id,
    ): Response {
        $post = $postsRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('No post found for id ' . $id);
        }

        if (1 !== $post->getOwnerId()) {
            throw new AccessDeniedException('You are not allowed to delete this post.');
        }

        $postsRepository->delete($id);

        return new Response('', Response::HTTP_OK);
    }

    #[Route('/forum/upvote', name: 'app_forum_upvote', methods: ['POST'])]
    public function upvote(
        Request $request,
        LikesRepository $likesRepository,
        PostsRepository $postsRepository,
    ): Response {
        $postId = $request->request->get('postId');
        if (empty($postId)) {
            return new JsonResponse(['success' => false, 'message' => 'Post ID is missing or invalid'], 400);
        }

        if (!$postsRepository->find($postId)) {
            return new JsonResponse(['success' => false, 'message' => 'Post not found'], 404);
        }

        $userId = 1;
        $likesRepository->handleVote($userId, $postId, true);
        $isLiked = $likesRepository->isLikedByUser($userId, $postId);

        return $this->render('components/VoteButtons.html.twig', [
            'postId' => $postId,
            'isLiked' => null === $isLiked ? null : (bool) $isLiked,
            'likesCount' => $likesRepository->likesCounter($postId),
            'dislikesCount' => $likesRepository->dislikesCounter($postId),
        ]);
    }

    #[Route('/forum/downvote', name: 'app_forum_downvote', methods: ['POST'])]
    public function downvote(
        Request $request,
        LikesRepository $likesRepository,
        PostsRepository $postsRepository,
    ): Response {
        $postId = $request->request->get('postId');
        if (empty($postId)) {
            return new JsonResponse(['success' => false, 'message' => 'Post ID is missing or invalid'], 400);
        }

        $userId = 1;

        $likesRepository->handleVote($userId, $postId, false);

        $post = $postsRepository->find($postId);

        if (!$post) {
            throw $this->createNotFoundException('No post found for id ' . $postId);
        }

        $isLiked = $likesRepository->isLikedByUser($userId, $postId);

        return $this->render('components/VoteButtons.html.twig', [
            'postId' => $postId,
            'isLiked' => null === $isLiked ? null : (bool) $isLiked,
            'likesCount' => $likesRepository->likesCounter($postId),
            'dislikesCount' => $likesRepository->dislikesCounter($postId),
        ]);
    }

    #[Route('/forum/comment', name: 'app_forum_comment', methods: ['POST'])]
    public function comment(
        Request $request,
        CommentsRepository $commentsRepository,
        UsersRepository $userRepository,
        PostsRepository $postsRepository,
        ValidatorInterface $validator,
    ): Response {
        $postId = $request->request->get('postId');
        $commentText = $request->request->get('commentText');
        $userId = 1;

        $comment = new Comments();
        $post = $postsRepository->find($postId);
        if (!$post) {
            return new JsonResponse(['error' => 'Post not found.'], 404);
        }
        $comment->setPost($post);
        $comment->setCommenterId($userId);
        $comment->setComment($commentText);
        $comment->setCommentedAt(new \DateTime());
        $comment->setUpdatedAt(new \DateTime());

        $errors = $validator->validate($comment);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['error' => $errorMessages], 400);
        }

        $commentsRepository->add($comment, $post);

        $user = $userRepository->find($userId);

        return $this->render('components/Comment.html.twig', [
            'commentId' => $comment->getCommentId(),
            'comment' => $comment->getComment(),
            'commentedAt' => $comment->getCommentedAt(),
            'updatedAt' => $comment->getUpdatedAt(),
            'name' => $user ? $user->getName() : 'Unknown',
            'lastName' => $user ? $user->getLastName() : 'User',
            'commenterId' => $comment->getCommenterId(),
        ]);
    }

    #[Route('/forum/delete/comment/{id}', name: 'comment_delete', methods: ['POST'])]
    public function deleteComment(
        CommentsRepository $commentsRepository,
        int $id,
    ): Response {
        $comment = $commentsRepository->find($id);

        if (!$comment) {
            throw $this->createNotFoundException('No comment found for id ' . $id);
        }

        if (1 !== $comment->getCommenterId()) {
            throw new AccessDeniedException('You are not allowed to delete this comment.');
        }

        $commentsRepository->delete($id);

        return new Response('', Response::HTTP_OK);
    }

    #[Route('/forum/edit/comment/{id}', name: 'comment_edit', methods: ['POST'])]
    public function editComment(
        Request $request,
        CommentsRepository $commentsRepository,
        PostsRepository $postsRepository,
        ValidatorInterface $validator,
        int $id,
    ): Response {
        $comment = $commentsRepository->find($id);

        if (!$comment) {
            return new JsonResponse(['error' => 'Comment not found.'], 404);
        }

        if (1 !== $comment->getCommenterId()) {
            return new JsonResponse(['error' => 'Unauthorized.'], 403);
        }

        $commentText = $request->request->get('editTextarea-' . $id);
        $comment->setComment($commentText);
        $comment->setUpdatedAt(new \DateTime());
        $post = $postsRepository->find($comment->getPostId());
        if (!$post) {
            return new JsonResponse(['error' => 'Post not found.'], 404);
        }
        $comment->setPost($post);
        $errors = $validator->validate($comment);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['error' => $errorMessages], 400);
        }

        $commentsRepository->update($comment, $post);

        return $this->render('components/CommentText.html.twig', [
            'commentId' => $comment->getCommentId(),
            'comment' => $comment->getComment(),
        ]);
    }

    #[Route('/dashboard/forum', name: 'app_dashboard_forum')]
    public function dashboard(
        PostsRepository $postsRepository,
        CommentsRepository $commentsRepository,
        UsersRepository $usersRepository,
    ): Response {
        $posts = $postsRepository->fetchPosts(1, 10, 1);

        foreach ($posts as &$post) {
            $user = $usersRepository->find($post['ownerId']);
            $post['ownerName'] = $user ? $user->getName() . ' ' . $user->getLastName() : 'Unknown User';

            $comments = $commentsRepository->fetchById($post['postId']);

            foreach ($comments as &$comment) {
                $commenter = $usersRepository->find($comment['commenterId']);
                $comment['commenterName'] = $commenter ? $commenter->getName() . ' ' . $commenter->getLastName() : 'Unknown User';
            }

            $post['comments'] = $comments;
        }

        return $this->render('dashboard/forum/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/forum/search', name: 'app_forum_search', methods: ['GET'])]
    public function searchByTextContent(Request $request, PostsRepository $postsRepository, CommentsRepository $commentsRepository, LikesRepository $likesRepository, PostImagesRepository $postImagesRepository): Response
    {
        $searchTerm = $request->query->get('q', '');
        $userId = 1;
        if (empty($searchTerm)) {
            return $this->redirectToRoute('app_forum');
        }
        $offset = max(0, (int) $request->query->get('offset', 0));
        $limit = min(100, max(1, (int) $request->query->get('limit', 10)));
        $posts = $postsRepository->searchByTextContent($searchTerm, $offset, $limit);
        foreach ($posts as &$post) {
            $post = $this->enrichPost($post, $userId, $commentsRepository, $likesRepository, $postImagesRepository);
        }

        return $this->render('forum/index.html.twig', [
            'posts' => $posts,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/forum/sort', name: 'app_forum_sort', methods: ['GET'])]
    public function sortPosts(Request $request, PostsRepository $postsRepository, CommentsRepository $commentsRepository, LikesRepository $likesRepository, PostImagesRepository $postImagesRepository): Response
    {
        $sortBy = $request->query->get('sortBy', '');
        if (empty($sortBy)) {
            return $this->redirectToRoute('app_forum');
        }
        $offset = max(0, (int) $request->query->get('offset', 0));
        $limit = min(100, max(1, (int) $request->query->get('limit', 10)));
        $userId = 1;
        switch ($sortBy) {
            case 'date_asc':
                $posts = $postsRepository->fetchPostsSorted('date_asc', $offset, $limit, $userId);
                break;
            case 'date_desc':
                $posts = $postsRepository->fetchPostsSorted('date_desc', $offset, $limit, $userId);
                break;
            case 'hot':
                $posts = $postsRepository->fetchPostsSorted('hot', $offset, $limit, $userId);
                break;
            default:
                $posts = $postsRepository->fetchPostsSorted('date_desc', $offset, $limit, $userId);
        }
        foreach ($posts as &$post) {
            $post = $this->enrichPost($post, $userId, $commentsRepository, $likesRepository, $postImagesRepository);
        }

        return $this->render('forum/index.html.twig', [
            'posts' => $posts,
            'sortBy' => $sortBy,
        ]);
    }
}
