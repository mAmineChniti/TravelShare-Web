<?php

namespace App\Controller;

use App\Entity\Likes;
use App\Entity\Posts;
use App\Entity\Comments;
use App\Repository\LikesRepository;
use App\Repository\PostsRepository;
use App\Repository\UsersRepository;
use App\Repository\CommentsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ForumController extends AbstractController
{
    #[Route('/forum', name: 'app_forum')]
    public function index(
        PostsRepository $postsRepository,
        CommentsRepository $commentsRepository,
        LikesRepository $likesRepository,
    ): Response {
        $userId = 1;
        $posts = $postsRepository->fetchPosts(0, 10, $userId);
        foreach ($posts as &$post) {
            $post['comments'] = $commentsRepository->fetchById($post['postId']);
            $post['likesCount'] = $likesRepository->likesCounter($post['postId']);
            $post['isLiked'] = $likesRepository->isLikedByUser($userId, $post['postId']);
        }

        return $this->render('forum/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/forum/post', name: 'app_forum_post', methods: ['POST'])]
    public function post(
        Request $request,
        PostsRepository $postsRepository,
        UsersRepository $userRepository,
        ValidatorInterface $validator,
    ): Response {
        $postText = $request->request->get('postText');
        $userId = 1;

        $post = new Posts();
        $post->setTextContent($postText);
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

        $user = $userRepository->find($userId);

        $postData = [
            'postId' => $post->getPostId(),
            'name' => $user->getName(),
            'lastName' => $user->getLastName(),
            'textContent' => $post->getTextContent(),
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

        $postText = $request->request->get('editTextarea-'.$id);
        $post->setTextContent($postText);
        $post->setUpdatedAt(new \DateTime());

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
            throw $this->createNotFoundException('No post found for id '.$id);
        }

        if (1 !== $post->getOwnerId()) {
            throw new AccessDeniedException('You are not allowed to delete this post.');
        }

        $postsRepository->delete($id);

        return new Response('', Response::HTTP_OK);
    }

    #[Route('/forum/like', name: 'app_forum_like', methods: ['POST'])]
    public function like(
        Request $request,
        LikesRepository $likesRepository,
        PostsRepository $postsRepository,
    ): Response {
        $postId = $request->request->get('postId');
        $userId = 1;

        if (empty($postId)) {
            return new JsonResponse(['success' => false, 'message' => 'Post ID is missing'], 400);
        }

        $like = new Likes();
        $like->setPostId($postId);
        $like->setLikerId($userId);

        $likesRepository->addOrRemove($like);

        $post = $postsRepository->find($postId);

        if (!$post) {
            throw $this->createNotFoundException('No post found for id '.$postId);
        }

        $isLiked = $likesRepository->isLikedByUser($userId, $postId);
        $likesCount = $likesRepository->likesCounter($postId);

        return $this->render('components/LikeButton.html.twig', [
            'postId' => $postId,
            'isLiked' => $isLiked,
            'likesCount' => $likesCount,
        ]);
    }

    #[Route('/forum/comment', name: 'app_forum_comment', methods: ['POST'])]
    public function comment(
        Request $request,
        CommentsRepository $commentsRepository,
        UsersRepository $userRepository,
        ValidatorInterface $validator,
    ): Response {
        $postId = $request->request->get('postId');
        $commentText = $request->request->get('commentText');
        $userId = 1;

        $comment = new Comments();
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

        $commentsRepository->add($comment);

        $user = $userRepository->find($userId);

        return $this->render('components/Comment.html.twig', [
            'commentId' => $comment->getCommentId(),
            'comment' => $comment->getComment(),
            'commentedAt' => $comment->getCommentedAt(),
            'updatedAt' => $comment->getUpdatedAt(),
            'name' => $user ? $user->getName() : 'User',
            'lastName' => $user ? $user->getLastName() : 'Name',
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
            throw $this->createNotFoundException('No comment found for id '.$id);
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

        $commentText = $request->request->get('editTextarea-'.$id);
        $comment->setComment($commentText);
        $comment->setUpdatedAt(new \DateTime());

        $errors = $validator->validate($comment);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['error' => $errorMessages], 400);
        }

        $commentsRepository->update($comment);

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
            $post['ownerName'] = $user ? $user->getName().' '.$user->getLastName() : 'Unknown User';

            $comments = $commentsRepository->fetchById($post['postId']);

            foreach ($comments as &$comment) {
                $commenter = $usersRepository->find($comment['commenterId']);
                $comment['commenterName'] = $commenter ? $commenter->getName().' '.$commenter->getLastName() : 'Unknown User';
            }

            $post['comments'] = $comments;
        }

        return $this->render('dashboard/forum/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}
