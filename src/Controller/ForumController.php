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

        return $this->render('components/like_button.html.twig', [
            'postId' => $postId,
            'isLiked' => $isLiked,
            'likesCount' => $likesCount,
        ]);
    }
}
