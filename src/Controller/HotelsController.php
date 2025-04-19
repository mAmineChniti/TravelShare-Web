<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HotelsController extends AbstractController
{
    #[Route('/hotels', name: 'app_hotels')]
    public function index(): Response
    {
        return $this->render('hotels/index.html.twig', [
            'controller_name' => 'HotelsController',
        ]);
    }
}
