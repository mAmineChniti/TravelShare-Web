<?php

namespace App\Controller;

use App\Repository\ExcursionsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExcursionsClientController extends AbstractController
{
    #[Route('/excursions/client', name: 'app_excursions_client')]
    public function index(ExcursionsRepository $excursionsRepository): Response
    {
        $excursions = $excursionsRepository->findAllWithGuides();

        return $this->render('excursions_client/index.html.twig', [
            'excursions' => $excursions,
        ]);
    }

    #[Route('/excursions/client/{id}', name: 'app_detail_excursions')]
    public function show(int $id, ExcursionsRepository $excursionsRepository): Response
    {
        $excursion = $excursionsRepository->find($id);

        if (!$excursion) {
            throw $this->createNotFoundException('Excursion non trouvée');
        }

        return $this->render('detail_excursions/index.html.twig', [
            'excursion' => $excursion,
        ]);
    }
}
