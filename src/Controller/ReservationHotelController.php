<?php

namespace App\Controller;

use App\Repository\ChambresRepository;
use App\Entity\ReservationHotel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationHotelController extends AbstractController
{
    #[Route('/hotel/{hotelId}/chambres/{chambreId}/reservation', name: 'app_reservation')]
    public function reserver(
        int $hotelId,
        int $chambreId,
        Request $request,
        EntityManagerInterface $em,
        ChambresRepository $chambresRepository
    ): Response {
        $chambre = $chambresRepository->find($chambreId);

        if (!$chambre) {
            throw $this->createNotFoundException('Chambre non trouvée !');
        }

        $errors = [];
        $success = false;

        if ($request->isMethod('POST')) {
            try {
                $dateDebut = new \DateTime($request->request->get('date_debut'));
                $dateFin = new \DateTime($request->request->get('date_fin'));
                $today = new \DateTime();
                $today->setTime(0, 0, 0);

                if ($dateDebut < $today) {
                    $errors[] = 'La date de début ne peut pas être dans le passé.';
                }

                if ($dateFin <= $dateDebut) {
                    $errors[] = 'La date de fin doit être après la date de début.';
                }

                if (empty($errors)) {
                    $reservation = new ReservationHotel();
                    $reservation->setChambreId($chambre->getChambreId());
                    $reservation->setDateDebut($dateDebut);
                    $reservation->setDateFin($dateFin);
                    $reservation->setStatusEnu('en attente');

                    $nbJours = $dateDebut->diff($dateFin)->days;
                    $prixTotal = $nbJours * $chambre->getPrixParNuit();
                    $reservation->setPrixTotale($prixTotal);
                    $reservation->setClientId(1); // À rendre dynamique si login

                    $em->persist($reservation);
                    $em->flush();

                    $success = true;
                    $this->RedirectToRoute('app_hotels');
                }
            } catch (\Exception $e) {
                $errors[] = 'Format de date invalide.';
            }
        }

        return $this->render('reservationchambre/index.html.twig', [
            'chambre' => $chambre,
            'errors' => $errors,
            'success' => $success
        ]);
    }
}
