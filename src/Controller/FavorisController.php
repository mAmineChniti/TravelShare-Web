<?php

namespace App\Controller;

use App\Entity\ListeFavoris;
use App\Entity\Excursions;
use App\Repository\ListeFavorisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FavorisController extends AbstractController
{
    #[Route('/favoris/toggle/{id}', name: 'toggle_favoris', methods: ['POST'])]
    public function toggleFavoris(
        Excursions $excursion,
        Request $request,
        EntityManagerInterface $em,
        Security $security,
        ListeFavorisRepository $favorisRepo
    ): JsonResponse {
        $user = $security->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $existingFavori = $favorisRepo->findOneBy([
            'user' => $user,
            'excursion' => $excursion
        ]);

        $isFavorite = false;
        
        if ($existingFavori) {
            $em->remove($existingFavori);
            $em->flush();
        } else {
            $favori = new ListeFavoris();
            $favori->setUser($user);
            $favori->setExcursion($excursion);
            
            $em->persist($favori);
            $em->flush();
            $isFavorite = true;
        }

        return new JsonResponse([
            'success' => true,
            'isFavorite' => $isFavorite
        ]);
    }
    #[Route('/favoris/check/{id}', name: 'check_favoris', methods: ['GET'])]
public function checkFavoris(
    Excursions $excursion,
    Security $security,
    ListeFavorisRepository $favorisRepo
): JsonResponse {
    $user = $security->getUser();
    
    if (!$user) {
        return new JsonResponse(['error' => 'User not authenticated'], 401);
    }

    $existingFavori = $favorisRepo->findOneBy([
        'user' => $user,
        'excursion' => $excursion
    ]);

    return new JsonResponse([
        'isFavorite' => $existingFavori !== null
    ]);
}
#[Route('/favoris', name: 'app_favoris')]
public function listFavoris(Security $security, ListeFavorisRepository $favorisRepo): Response
{
    $user = $security->getUser();
    
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    $favoris = $favorisRepo->findBy(['user' => $user]);

    $excursions = array_map(function($favori) {
        return $favori->getExcursion();
    }, $favoris);

    return $this->render('favoris/index.html.twig', [
        'excursions' => $excursions,
    ]);
}
}