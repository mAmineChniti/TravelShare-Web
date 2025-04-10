<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ExcursionsController extends AbstractController
{
    #[Route('/excursions', name: 'app_excursions')]
    public function index(): Response
    {
        return $this->render('excursions/index.html.twig', [
            'controller_name' => 'ExcursionsController',
        ]);
    }
}
