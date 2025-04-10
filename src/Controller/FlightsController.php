<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FlightsController extends AbstractController
{
    #[Route('/flights', name: 'app_flights')]
    public function index(): Response
    {
        return $this->render('flights/index.html.twig', [
            'controller_name' => 'FlightsController',
        ]);
    }
}
