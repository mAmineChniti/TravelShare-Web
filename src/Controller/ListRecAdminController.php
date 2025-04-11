<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListRecAdminController extends AbstractController
{
    #[Route('/list/rec/admin', name: 'app_list_rec_admin')]
    public function index(): Response
    {
        return $this->render('list_rec_admin/index.html.twig', [
            'controller_name' => 'ListRecAdminController',
        ]);
    }
}
