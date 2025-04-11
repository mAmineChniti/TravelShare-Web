<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilAdminController extends AbstractController
{
    #[Route('/accueil/admin', name: 'app_accueil_admin')]
    public function index(): Response
    {
        return $this->render('accueil_admin/index.html.twig', [
            'controller_name' => 'AccueilAdminController',
        ]);
    }
}
