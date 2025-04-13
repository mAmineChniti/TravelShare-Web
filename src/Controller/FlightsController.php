<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OffresVoyageRepository;
final class FlightsController extends AbstractController
{
    #[Route('/flights', name: 'app_flights')]
    public function index(OffresVoyageRepository $voyageService): Response
    {
        $voyages = $voyageService->listAll();

        return $this->render('flights/index.html.twig', [
            'voyages' => $voyages,
        ]);
    }
}
