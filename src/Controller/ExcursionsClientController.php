<?php

namespace App\Controller;

use App\Repository\ExcursionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class ExcursionsClientController extends AbstractController
{
    #[Route('/excursions/client', name: 'app_excursions_client')]
public function index(Request $request, ExcursionsRepository $excursionsRepository): Response
{
    $searchTitle = $request->query->get('title');
    $maxPrice = $request->query->get('max_price');
    
    $excursions = $excursionsRepository->findByCriteria([
        'title' => $searchTitle,
        'max_price' => $maxPrice
    ]);
    
    return $this->render('excursions_client/index.html.twig', [
        'excursions' => $excursions,
        'searchTitle' => $searchTitle,
        'maxPrice' => $maxPrice
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