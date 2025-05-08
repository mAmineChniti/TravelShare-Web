<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Users;
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
        if ($images) {
            $post['images'] = $images ? array_map(fn ($image) => base64_encode($image), $images) : [];
        } else {
            $post['images'] = [];
        }

        return $post;
    }

    private function getRecommendedPosts(array $postsToBeAnalyzed, int $userId, PostsRepository $postsRepository, CommentsRepository $commentsRepository, LikesRepository $likesRepository, PostImagesRepository $postImagesRepository): array
    {
        $postContents = array_map(function ($post) {
            return [
                'postId' => $post->getPostId(),
                'textContent' => $post->getTextContent(),
            ];
        }, $postsToBeAnalyzed);

        $geminiApiKey = $this->getParameter('app.gemini_api_key');

        if (!$geminiApiKey) {
            throw new \RuntimeException('API key is missing.');

            return [];
        }

        $recommendedPostIds = [];
        try {
            $httpClient = HttpClient::create();
            $response = $httpClient->request('POST', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key='.$geminiApiKey, [
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

            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                $rawPostIds = $responseData['candidates'][0]['content']['parts'][0]['text'];

                $rawPostIds = preg_replace('/^```json\\n|\\n```$/', '', $rawPostIds);
                $recommendedPostIds = json_decode($rawPostIds, true);

                if (JSON_ERROR_NONE !== json_last_error()) {
                    $recommendedPostIds = [];
                }
            }
        } catch (\Exception) {
            $recommendedPostIds = [];
        }

        $recommendedPosts = [];
        if (!empty($recommendedPostIds)) {
            $recommendedPosts = $postsRepository->fetchPostsByIds($recommendedPostIds);
            if (!empty($recommendedPosts)) {
                foreach ($recommendedPosts as &$post) {
                    $post = $this->enrichPost($post, $userId, $commentsRepository, $likesRepository, $postImagesRepository);
                }
            }
        }

        return $recommendedPosts;
    }

    #[Route('/forum', name: 'app_forum', methods: ['GET'])]
    public function index(
        Request $request,
        PostsRepository $postsRepository,
        CommentsRepository $commentsRepository,
        LikesRepository $likesRepository,
        PostImagesRepository $postImagesRepository,
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Users) {
            return $this->redirectToRoute('app_login');
        }
        $userId = $user->getUserId();
        $offset = max(0, (int) $request->query->get('offset', 0));
        $limit = min(100, max(1, (int) $request->query->get('limit', 10)));

        $posts = $postsRepository->fetchPosts($offset, $limit, $userId);
        foreach ($posts as &$post) {
            $post = $this->enrichPost($post, $userId, $commentsRepository, $likesRepository, $postImagesRepository);
        }

        $postsToBeAnalyzed = $postsRepository->listAll();
        $recommendedPosts = $this->getRecommendedPosts($postsToBeAnalyzed, $userId, $postsRepository, $commentsRepository, $likesRepository, $postImagesRepository);
        if (!empty($recommendedPosts)) {
            $recommendedPostIds = array_column($recommendedPosts, 'postId');
            $posts = array_filter($posts, function ($post) use ($recommendedPostIds) {
                return !in_array($post['postId'], $recommendedPostIds);
            });
        }

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
        $user = $this->getUser();
        if (!$user instanceof Users) {
            throw new AccessDeniedException('You are not allowed to access this page.');
        }
        $userId = $user->getUserId();
        $post = new Posts();
        $post->setTextContent($postText);
        $post->setPostTitle($postTitle);
        $post->setOwnerId($userId);
        $post->setCreatedAt(new \DateTime());
        $post->setUpdatedAt(new \DateTime());
        $post->setPostUnique(uniqid());
        $post->setSlug($post->getPostTitle().''.$post->getPostUnique());

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
            'ownerId' => $post->getOwnerId(),
            'postId' => $post->getPostId(),
            'name' => $user->getName(),
            'lastName' => $user->getLastName(),
            'textContent' => $post->getTextContent(),
            'postTitle' => $post->getPostTitle(),
            'slug' => $post->getSlug(),
            'images' => $images ? array_map(fn ($image) => base64_encode($image), $images) : [],
            'isLiked' => null,
            'likesCount' => 0,
            'dislikesCount' => 0,
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
        $user = $this->getUser();
        if (!$user instanceof Users) {
            throw new AccessDeniedException('You are not allowed to access this page.');
        }
        $userId = $user->getUserId();
        if (!$userId) {
            throw new AccessDeniedException('You are not allowed to access this page.');
        }
        if (!$userId) {
            return new JsonResponse(['error' => 'User ID is missing or invalid'], 400);
        }
        if (!$post) {
            return new JsonResponse(['error' => 'Post not found.'], 404);
        }

        if ((string) $userId !== (string) $post->getOwnerId() && !$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse([
                'error' => 'Unauthorized.',
                'debug' => [
                    'userId' => $userId,
                    'ownerId' => $post->getOwnerId(),
                    'comparison' => (string) $userId === (string) $post->getOwnerId(),
                ],
            ], 403);
        }

        $postText = $request->request->get('editTextarea-'.$id);
        $post->setTextContent($postText);
        $post->setUpdatedAt(new \DateTime());

        $postTitle = $request->request->get('editTitle-'.$id);
        $post->setPostTitle($postTitle);

        $errors = $validator->validate($post);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['error' => $errorMessages], 400);
        }

        if (!$postText || !$postTitle) {
            return new JsonResponse(['error' => 'Both title and content are required.'], 400);
        }

        if ($postTitle !== $post->getPostTitle()) {
            $post->setSlug($postTitle.'-'.uniqid());
        }

        $postsRepository->update($post);

        return $this->render('components/PostText.html.twig', [
            'slug' => $post->getSlug(),
            'postId' => $post->getPostId(),
            'postTitle' => $post->getPostTitle(),
            'textContent' => $post->getTextContent(),
        ]);
    }

    #[Route('/forum/delete/{id}', name: 'app_forum_delete', methods: ['POST'])]
    public function delete(
        PostsRepository $postsRepository,
        Request $request,
        int $id,
    ): Response {
        $post = $postsRepository->find($id);
        $user = $this->getUser();
        if (!$user instanceof Users) {
            throw new AccessDeniedException('You are not allowed to access this page.');
        }
        $userId = $user->getUserId();
        if (!$post) {
            throw $this->createNotFoundException('No post found for id '.$id);
        }

        if ((string) $userId !== (string) $post->getOwnerId() && !$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['error' => 'Unauthorized.'], 403);
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
        $user = $this->getUser();
        if (!$user instanceof Users) {
            return new JsonResponse(['success' => false, 'message' => 'User not found'], 404);
        }
        $userId = $user->getUserId();
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

        $user = $this->getUser();
        if (!$user instanceof Users) {
            return new JsonResponse(['success' => false, 'message' => 'User not found'], 404);
        }
        $userId = $user->getUserId();

        $likesRepository->handleVote($userId, $postId, false);

        $post = $postsRepository->find($postId);

        if (!$post) {
            throw $this->createNotFoundException('No post found for id '.$postId);
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
        $user = $this->getUser();
        if (!$user instanceof Users) {
            throw new AccessDeniedException('You are not allowed to access this page.');
        }
        $userId = $user->getUserId();
        $post = $postsRepository->findOneBy(['postId' => $postId]);
        if (!$post) {
            return new JsonResponse(['error' => 'Post not found.'], 404);
        }
        $comment = new Comments();
        $comment->setPost($post);
        $comment->setPostId($postId);
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
        Request $request,
        int $id,
    ): Response {
        $comment = $commentsRepository->find($id);
        $user = $this->getUser();
        if (!$user instanceof Users) {
            throw new AccessDeniedException('You are not allowed to access this page.');
        }
        $userId = $user->getUserId();

        if (!$comment) {
            throw $this->createNotFoundException('No comment found for id '.$id);
        }

        if ((string) $userId !== (string) $comment->getCommenterId() && !$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['error' => 'Unauthorized.'], 403);
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
        $user = $this->getUser();
        if (!$user instanceof Users) {
            throw new AccessDeniedException('You are not allowed to access this page.');
        }
        $userId = $user->getUserId();
        if (!$comment) {
            return new JsonResponse(['error' => 'Comment not found.'], 404);
        }

        if ((string) $userId !== (string) $comment->getCommenterId() && !$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['error' => 'Unauthorized.'], 403);
        }

        $commentText = $request->request->get('editTextarea-'.$id);
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
        LikesRepository $likesRepository,
        PostImagesRepository $postImagesRepository,
        Request $request,
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Users) {
            return $this->redirectToRoute('app_login');
        }
        if(!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_forum');
        }
        $userId = $user->getUserId();
        $offset = max(0, (int) $request->query->get('offset', 0));
        $limit = min(100, max(1, (int) $request->query->get('limit', 10)));
        $posts = $postsRepository->fetchPosts($offset, $limit, $userId);
        foreach ($posts as &$post) {
            $post = $this->enrichPost($post, $userId, $commentsRepository, $likesRepository, $postImagesRepository);
        }

        return $this->render('dashboard/forum/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/forum/search', name: 'app_forum_search', methods: ['GET'])]
    public function searchByTextContent(Request $request, PostsRepository $postsRepository, CommentsRepository $commentsRepository, LikesRepository $likesRepository, PostImagesRepository $postImagesRepository): Response
    {
        $searchTerm = $request->query->get('q', '');
        $user = $this->getUser();
        if (!$user instanceof Users) {
            return $this->redirectToRoute('app_login');
        }
        $userId = $user->getUserId();
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
        $user = $this->getUser();
        if (!$user instanceof Users) {
            throw new AccessDeniedException('You are not allowed to access this page.');
        }
        $userId = $user->getUserId();
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

        $postsToBeAnalyzed = $postsRepository->listAll();
        $recommendedPosts = $this->getRecommendedPosts($postsToBeAnalyzed, $userId, $postsRepository, $commentsRepository, $likesRepository, $postImagesRepository, $userId);

        if (!empty($recommendedPosts)) {
            $recommendedPostIds = array_column($recommendedPosts, 'postId');
            $posts = array_filter($posts, function ($post) use ($recommendedPostIds) {
                return !in_array($post['postId'], $recommendedPostIds);
            });
        }

        return $this->render('forum/index.html.twig', [
            'posts' => $posts,
            'sortBy' => $sortBy,
            'recommendedPosts' => $recommendedPosts,
        ]);
    }

    #[Route('/forum/post/{slug}', name: 'app_forum_post_by_slug', methods: ['GET'])]
    public function viewPostBySlug(
        string $slug,
        PostsRepository $postsRepository,
        CommentsRepository $commentsRepository,
        LikesRepository $likesRepository,
        PostImagesRepository $postImagesRepository,
        UsersRepository $usersRepository,
    ): Response {
        $post = $postsRepository->findOneBy(['slug' => $slug]);

        if (!$post) {
            throw $this->createNotFoundException('Post not found.');
        }

        $user = $this->getUser();
        if (!$user instanceof Users) {
            throw new AccessDeniedException('You are not allowed to access this page.');
        }
        $userId = $user->getUserId();
        $user = $usersRepository->find($post->getOwnerId());
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }
        $userName = $user->getName();
        $userLastName = $user->getLastName();
        if (!$userName || !$userLastName) {
            throw $this->createNotFoundException('User name or last name not found.');
        }
        $postData = [
            'ownerId' => $post->getOwnerId(),
            'postId' => $post->getPostId(),
            'name' => $userName,
            'lastName' => $userLastName,
            'textContent' => $post->getTextContent(),
            'postTitle' => $post->getPostTitle(),
            'createdAt' => $post->getCreatedAt(),
            'updatedAt' => $post->getUpdatedAt(),
            'slug' => $post->getSlug(),
        ];
        $post = $this->enrichPost($postData, $userId, $commentsRepository, $likesRepository, $postImagesRepository);

        return $this->render('forum/post.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/forum/validate-image', name: 'app_forum_validate_image', methods: ['POST'])]
    public function validateImage(Request $request): JsonResponse
    {
        $uploadedFiles = $request->files->get('postImages');
        if (!$uploadedFiles) {
            return new JsonResponse(['error' => 'No images uploaded.'], 400);
        }

        $apiUser = $this->getParameter('app.image_profanity_api_user');
        $apiSecret = $this->getParameter('app.image_profanity_api_key');
        $isInappropriate = false;

        foreach ($uploadedFiles as $uploadedFile) {
            try {
                $filePath = $uploadedFile->getPathname();
                $httpClient = HttpClient::create();
                $response = $httpClient->request('POST', 'https://api.sightengine.com/1.0/check.json', [
                    'headers' => [
                        'Content-Type' => 'multipart/form-data',
                    ],
                    'body' => [
                        'media' => fopen($filePath, 'r'),
                        'models' => 'nudity-2.1,weapon,recreational_drug,medical,offensive-2.0,text-content,face-attributes,gore-2.0,text,violence,self-harm',
                        'api_user' => $apiUser,
                        'api_secret' => $apiSecret,
                    ],
                ]);

                $responseData = $response->toArray();
                if (
                    $responseData['nudity']['sexual_activity'] > 0.01
                    || $responseData['nudity']['sexual_display'] > 0.01
                    || $responseData['nudity']['erotica'] > 0.01
                    || $responseData['weapon']['classes']['firearm'] > 0.01
                    || $responseData['recreational_drug']['prob'] > 0.01
                    || $responseData['medical']['prob'] > 0.01
                    || $responseData['offensive']['nazi'] > 0.01
                    || $responseData['gore']['prob'] > 0.01
                    || $responseData['violence']['prob'] > 0.01
                    || $responseData['self-harm']['prob'] > 0.01
                ) {
                    $isInappropriate = true;
                    break;
                }
            } catch (\Exception) {
                return new JsonResponse(['error' => 'An error occurred while validating the image.'], 500);
            }
        }

        return new JsonResponse(['isInappropriate' => $isInappropriate], 200);
    }
}
