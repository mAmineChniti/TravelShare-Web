<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PostsRepository;
use App\Repository\CommentsRepository;
use App\Repository\LikesRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Posts;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Comments;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ForumController extends AbstractController
{
    #[Route('/forum', name: 'app_forum')]
    public function index(
        PostsRepository $postsRepository,
        CommentsRepository $commentsRepository,
        LikesRepository $likesRepository
    ): Response {
        $userId = 1;
        $posts = $postsRepository->fetchPosts(0, 10, $userId);
        foreach ($posts as &$post) {
            $post['comments'] = $commentsRepository->fetchById($post['postId']);
            $post['likesCount'] = $likesRepository->likesCounter($post['postId']);
            $post['isLiked'] = $likesRepository->isLikedByUser($userId, $post['postId']);
        }

        return $this->render('forum/index.html.twig', [
            'controller_name' => 'ForumController',
            'posts' => $posts,
        ]);
    }

    #[Route('/forum/post', name: 'app_forum_post', methods: ['POST'])]
    public function post(
        Request $request,
        PostsRepository $postsRepository
    ): Response {
        $postText = $request->request->get('postText');

        if (empty($postText)) {
            return $this->redirectToRoute('app_forum');
        }

        $post = new Posts();
        $post->setTextContent($postText);
        $post->setOwnerId(1);
        $post->setCreatedAt(new \DateTimeImmutable(date('Y-m-d H:i:s')));
        $post->setUpdatedAt(new \DateTimeImmutable(date('Y-m-d H:i:s')));

        $postsRepository->add($post);

        return $this->redirectToRoute('app_forum');
    }

    #[Route('/forum/edit/{id}', name: 'app_forum_edit', methods: ['POST'])]
    public function edit(
        Request $request,
        PostsRepository $postsRepository,
        int $id
    ): Response {
        $post = $postsRepository->find($id);
        if (!$post) {
            throw $this->createNotFoundException('No post found for id ' . $id);
        }

        if ($post->getOwnerId() !== 1) {
            throw new AccessDeniedException('You are not allowed to edit this post.');
        }

        $postText = $request->request->get('editTextarea-' . $id);

        if ($postText === null || empty(trim($postText))) {
            $html = sprintf(
                '<div id="postText-%s"><p class="text-gray-600 mt-2">%s</p></div>',
                $id,
                htmlspecialchars($postText)
            );
            return new Response($html);
        }

        $post->setTextContent($postText);
        $post->setUpdatedAt(new \DateTimeImmutable());
        $postsRepository->update($post);
        $html = sprintf(
            '<div id="postText-%s"><p class="text-gray-600 mt-2">%s</p></div>',
            $id,
            htmlspecialchars($postText)
        );
        return new Response($html);
    }

    #[Route('/forum/delete/{id}', name: 'app_forum_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        PostsRepository $postsRepository,
        int $id
    ): Response {
        $post = $postsRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('No post found for id ' . $id);
        }

        if ($post->getOwnerId() !== 1) {
            throw new AccessDeniedException('You are not allowed to delete this post.');
        }

        if ($this->isCsrfTokenValid('delete' . $post->getPostId(), $request->request->get('_token'))) {
            $postsRepository->delete($id);
        }

        return $this->redirectToRoute('app_forum');
    }

    #[Route('/forum/like', name: 'app_forum_like', methods: ['POST'])]
    public function like(
        Request $request,
        LikesRepository $likesRepository,
        PostsRepository $postsRepository
    ): Response {
        $postId = $request->request->get('postId');
        $userId = 1;

        if (empty($postId)) {
            return new JsonResponse(['success' => false, 'message' => 'Post ID is missing'], 400);
        }

        $like = new \App\Entity\Likes();
        $like->setPostId($postId);
        $like->setLikerId($userId);

        $likesRepository->addOrRemove($like);

        $post = $postsRepository->find($postId);

        if (!$post) {
            throw $this->createNotFoundException('No post found for id ' . $postId);
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
    ): Response {
        $postId = $request->request->get('postId');
        $commentText = $request->request->get('commentText');
        $userId = 1;

        if (empty($postId) || empty($commentText)) {
            return new JsonResponse(['success' => false, 'message' => 'Post ID or comment text is missing'], 400);
        }

        $comment = new Comments();
        $comment->setPostId($postId);
        $comment->setCommenterId($userId);
        $comment->setComment($commentText);
        $comment->setCommentedAt(new \DateTimeImmutable(date('Y-m-d H:i:s')));
        $comment->setUpdatedAt(new \DateTimeImmutable(date('Y-m-d H:i:s')));

        $commentsRepository->add($comment);

        return $this->redirectToRoute('app_forum');
    }

    #[Route('/forum/delete/comment/{id}', name: 'comment_delete', methods: ['POST'])]
    public function deleteComment(
        Request $request,
        CommentsRepository $commentsRepository,
        int $id
    ): Response {
        $comment = $commentsRepository->find($id);

        if (!$comment) {
            throw $this->createNotFoundException('No comment found for id ' . $id);
        }

        if ($comment->getCommenterId() !== 1) {
            throw new AccessDeniedException('You are not allowed to delete this comment.');
        }

        if ($this->isCsrfTokenValid('delete' . $comment->getCommentId(), $request->request->get('_token'))) {
            $commentsRepository->delete($id);
        }

        return $this->redirectToRoute('app_forum');
    }

    #[Route('/forum/edit/comment/{id}', name: 'comment_edit', methods: ['POST'])]
    public function editComment(
        Request $request,
        CommentsRepository $commentsRepository,
        int $id
    ): Response {
        $comment = $commentsRepository->find($id);
        if (!$comment) {
            throw $this->createNotFoundException('No comment found for id ' . $id);
        }

        if ($comment->getCommenterId() !== 1) {
            throw new AccessDeniedException('You are not allowed to edit this comment.');
        }

        $commentText = $request->request->get('editTextarea-' . $id);

        if ($commentText === null || empty(trim($commentText))) {
            $html = sprintf(
                '<div id="commentText-%s"><p class="text-gray-600 mt-2">%s</p></div>',
                $id,
                htmlspecialchars($commentText)
            );
            return new Response($html);
        }

        $comment->setComment($commentText);
        $comment->setUpdatedAt(new \DateTimeImmutable());
        $commentsRepository->update($comment);
        $html = sprintf(
            '<div id="commentText-%s"><p class="text-gray-600 mt-2">%s</p></div>',
            $id,
            htmlspecialchars($commentText)
        );
        return new Response($html);
    }
}
