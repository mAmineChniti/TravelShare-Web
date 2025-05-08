<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ChartAdminController extends AbstractController
{
    #[Route('/chart/admin', name: 'app_chart_admin')]
    public function index(): Response
    {
        return $this->render('chart_admin/index.html.twig', [
            'controller_name' => 'ChartAdminController',
        ]);
    }
}
