<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccueilAdminController extends AbstractController
{
    #[Route('/accueil/admin', name: 'app_accueil_admin')]
    public function index(UsersRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $userRepository->createQueryBuilder('u')
            ->orderBy('u.name', 'ASC')
            ->getQuery();

        $users = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10,
            [
                'defaultSortFieldName' => 'u.name',
                'defaultSortDirection' => 'asc',
            ]
        );

        return $this->render('accueil_admin/index.html.twig', [
            'users' => $users,
            'current_menu' => 'admin_users',
        ]);
    }

    #[Route('/admin/user/toggle-block/{id}', name: 'admin_user_toggle_block', methods: ['POST'])]
    public function toggleBlock(Users $user, EntityManagerInterface $em): JsonResponse
    {
        $message = '';
        $success = false;

        try {
            // Vérification de l'état actuel du compte de l'utilisateur
            if ($user->isBlocked()) {
                // Si l'utilisateur est déjà bloqué, on le débloque
                $user->unblock();
                $message = 'User successfully unlocked';
            } else {
                // Si l'utilisateur n'est pas bloqué, on le bloque
                $user->block();
                $message = 'User successfully blocked';
            }

            // Sauvegarde des changements dans la base de données
            $em->flush();

            // En cas de succès
            $success = true;
        } catch (\Exception $e) {
            // En cas d'erreur
            $message = 'An error occurred while processing the request: '.$e->getMessage();
        }

        return $this->json([
            'success' => $success,
            'message' => $message,
            'isBlocked' => $user->isBlocked(),
            'newButtonText' => $user->isBlocked() ? 'Débloquer' : 'Bloquer',
            'newStatusText' => $user->isBlocked() ? 'Bloqué' : 'Actif',
        ]);
    }

    #[Route('/user/photo/{id}', name: 'user_photo', methods: ['GET'])]
    public function getUserPhoto(Users $user): Response
    {
        $photoContent = $user->getPhoto();

        if (!$photoContent) {
            return $this->file(
                $this->getParameter('kernel.project_dir').'/public/images2/9187604.png',
                '9187604.png'
            );
        }

        // Si c'est une ressource (cas typique avec Doctrine et BLOB)
        if (is_resource($photoContent)) {
            rewind($photoContent);
            $photoContent = stream_get_contents($photoContent);
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($photoContent) ?: 'application/octet-stream';

        return new Response(
            $photoContent,
            200,
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="user-'.$user->getUserId().'"',
            ]
        );
    }
}
