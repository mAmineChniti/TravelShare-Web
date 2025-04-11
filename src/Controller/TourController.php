<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TourController extends AbstractController
{
    #[Route('/tour', name: 'app_tour')]
    public function index(): Response
    {
        return $this->render('tour/index.html.twig', [
            'controller_name' => 'TourController',
        ]);
    }
}
