<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ErrorController extends AbstractController
{
    #[Route('/error', name: 'app_error')]
    public function index(): Response
    {
        return $this->render('error/index.html.twig', [
            'controller_name' => 'ErrorController',
        ]);
    }
}
