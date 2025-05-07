<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Notification;
use App\Repository\UsersRepository;
use App\Repository\ReclamationsRepository;
use App\Repository\ReponsesRepository;
use App\Repository\ExcursionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use finfo;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

class AccueilAdminController extends AbstractController
{
    #[Route('/accueil/admin', name: 'app_accueil_admin')]
    public function index(UsersRepository $userRepository, Request $request, PaginatorInterface $paginator, EntityManagerInterface $em): Response
    {
        // Vérification que l'utilisateur est un admin
        $user = $this->getUser();
        if ($user->getRole() !== 1) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à accéder à cette page.');
        }

        // Récupérer la liste des utilisateurs
        $query = $userRepository->createQueryBuilder('u')
            ->orderBy('u.name', 'ASC')
            ->getQuery();

        // Pagination des utilisateurs
        $users = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        // Récupérer les notifications de l'administrateur (utilisateur connecté)
        $notifications = $em->getRepository(Notification::class)->findBy(
            ['user' => $user], // Filtrage des notifications pour l'utilisateur admin
            ['createdAt' => 'DESC'] // Tri par date (les plus récentes en premier)
        );

        return $this->render('accueil_admin/index.html.twig', [
            'users' => $users,
            'notifications' => $notifications, // Passer les notifications à la vue
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
            $message = 'An error occurred while processing the request: ' . $e->getMessage();
        }

        return $this->json([
            'success' => $success,
            'message' => $message,
            'isBlocked' => $user->isBlocked(),
            'newButtonText' => $user->isBlocked() ? 'Débloquer' : 'Bloquer',
            'newStatusText' => $user->isBlocked() ? 'Bloqué' : 'Actif'
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

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($photoContent) ?: 'application/octet-stream';

        return new Response(
            $photoContent,
            200,
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="user-'.$user->getUserId().'"'
            ]
        );
    }

    #[Route('/admin/user/count', name: 'admin_user_count', methods: ['GET'])]
    public function getUserCount(UsersRepository $userRepository): JsonResponse
    {
        try {
            // Récupérer le nombre d'utilisateurs en comptant les entrées dans la table 'Users'
            $userCount = $userRepository->createQueryBuilder('u')
                ->select('COUNT(u.userId)') // Assurez-vous d'utiliser 'userId' qui est le bon identifiant dans votre entité
                ->getQuery()
                ->getSingleScalarResult(); // Cette méthode renvoie le nombre d'utilisateurs

            return $this->json([
                'success' => true,
                'userCount' => $userCount,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }
    }

    #[Route('/admin/reclamation/count', name: 'admin_reclamation_count', methods: ['GET'])]
    public function getReclamationCount(ReclamationsRepository $reclamationsRepository): JsonResponse
    {
        try {
            // Compter les réclamations via DQL (en respectant le nom correct du champ)
            $reclamationCount = $reclamationsRepository->createQueryBuilder('r')
                ->select('COUNT(r.reclamationId)')
                ->getQuery()
                ->getSingleScalarResult();

            return new JsonResponse([
                'success' => true,
                'reclamationCount' => $reclamationCount,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
            ]);
        }
    }

    #[Route('/admin/reponses/count', name: 'admin_reponses_count', methods: ['GET'])]
    public function getReponsesCount(ReponsesRepository $reponsesRepository): JsonResponse
    {
        try {
            $reponseCount = $reponsesRepository->createQueryBuilder('r')
                ->select('COUNT(r.reponseId)')
                ->getQuery()
                ->getSingleScalarResult();

            return new JsonResponse([
                'success' => true,
                'reponseCount' => $reponseCount,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
            ]);
        }
    }

    #[Route('/admin/excursions/count', name: 'admin_excursion_count', methods: ['GET'])]
    public function getExcursionCount(ExcursionsRepository $excursionsRepository): JsonResponse
    {
        try {
            // Récupérer le nombre d'excursions
            $excursionCount = $excursionsRepository->createQueryBuilder('e')
                ->select('COUNT(e.excursionId)') // Utilisez 'excursionId' pour correspondre à la colonne
                ->getQuery()
                ->getSingleScalarResult(); // Renvoie le nombre d'excursions

            return $this->json([
                'success' => true,
                'excursionCount' => $excursionCount,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }
    }

}