<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListRecUserController extends AbstractController
{
    #[Route('/list/rec/user', name: 'app_list_rec_user')]
    public function index(): Response
    {
        return $this->render('list_rec_user/index.html.twig', [
            'controller_name' => 'ListRecUserController',
        ]);
    }
}
