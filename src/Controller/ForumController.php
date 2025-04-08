<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PostsRepository;
use App\Repository\CommentsRepository;

final class ForumController extends AbstractController {
    #[Route('/forum', name: 'app_forum')]
    public function index(
        PostsRepository $postsRepository,
        CommentsRepository $commentsRepository
    ): Response {
        $userId = 1;
        $posts = $postsRepository->fetchPosts(0, 10, $userId);
        foreach ($posts as &$post) {
            $post['comments'] = $commentsRepository->fetchById($post['postId']);
        }

        return $this->render('forum/index.html.twig', [
            'controller_name' => 'ForumController',
            'posts' => $posts,
        ]);
    }
}