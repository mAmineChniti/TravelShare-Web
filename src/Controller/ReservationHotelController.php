<?php

namespace App\Controller;

use App\Repository\ChambresRepository;
use App\Entity\ReservationHotel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReservationHotelRepository;

class ReservationHotelController extends AbstractController
{
    #[Route('/hotel/{hotelId}/chambres/{chambreId}/reservation', name: 'app_reservation')]
    public function reserver(
        int $hotelId,
        int $chambreId,
        Request $request,
        ReservationHotelRepository $reservationRepository,
        ChambresRepository $chambresRepository
    ): Response {
        $chambre = $chambresRepository->find($chambreId);
    
        if (!$chambre) {
            throw $this->createNotFoundException('Chambre non trouvée !');
        }
    
        if ($request->isMethod('POST')) {
            $dateDebutInput = $request->request->get('date_debut');
            $dateFinInput = $request->request->get('date_fin');
        
            if (!$dateDebutInput || !$dateFinInput) {
                $this->addFlash('error', 'Veuillez remplir les deux dates.');
            } else {
                try {
                    $dateDebut = new \DateTime($dateDebutInput);
                    $dateFin = new \DateTime($dateFinInput);
                    $today = new \DateTime('today');
        
                    if (!$dateDebut || !$dateFin) {
                        $this->addFlash('error', 'Format de date invalide.');
                    } elseif ($dateDebut < $today) {
                        $this->addFlash('error', 'La date de début ne peut pas être dans le passé.');
                    } elseif ($dateFin <= $today) {
                        $this->addFlash('error', 'La date de fin ne peut pas être aujourd’hui ou dans le passé.');
                    } elseif ($dateFin <= $dateDebut) {
                        $this->addFlash('error', 'La date de fin doit être après la date de début.');
                    } else {
                        $reservation = new ReservationHotel();
                        $reservation->setChambreId($chambre->getChambreId());
                        $reservation->setDateDebut($dateDebut);
                        $reservation->setDateFin($dateFin);
                        $reservation->setStatusEnu('en attente');
        
                        $nbJours = $dateDebut->diff($dateFin)->days;
                        $reservation->setPrixTotale($nbJours * $chambre->getPrixParNuit());
                        $reservation->setClientId(1);
                        $reservationRepository->add($reservation, true);
        
                        $this->addFlash('success', 'Réservation enregistrée avec succès.');
return $this->redirectToRoute('app_reservation', [
    'hotelId' => $hotelId,
    'chambreId' => $chambreId
]);

                    }
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue pendant la réservation.');
                }
            }
        }
    
        return $this->render('reservationchambre/index.html.twig', [
            'chambre' => $chambre,
            'hotelId' => $hotelId,
            'chambreId' => $chambreId
        ]);
    }
}
