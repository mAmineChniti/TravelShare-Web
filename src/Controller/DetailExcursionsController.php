<?php

namespace App\Controller;

use App\Repository\ExcursionsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DetailExcursionsController extends AbstractController
{
    #[Route('/excursions/{id}', name: 'app_detail_excursions')]
    public function show(int $id, ExcursionsRepository $excursionsRepository): Response
    {
        $excursion = $excursionsRepository->find($id);

        if (!$excursion) {
            throw $this->createNotFoundException('Excursion not found');
        }

        return $this->render('detail_excursions/index.html.twig', [
            'excursion' => $excursion, // On passe une SEULE excursion
        ]);
    }
}
