<?php

namespace App\Controller;

use App\Repository\ChambresRepository;
use App\Entity\ReservationHotel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReservationHotelRepository;
use Psr\Log\LoggerInterface;

class ReservationHotelController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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
            throw $this->createNotFoundException('Chambre non trouvée.');
        }

        if ($request->isMethod('POST')) {
            $dateDebut = new \DateTime($request->request->get('date_debut'));
            $dateFin = new \DateTime($request->request->get('date_fin'));
            $today = new \DateTime();

            if ($dateDebut < $today) {
                $this->addFlash('error', 'La date de début ne peut pas être antérieure à aujourd\'hui.');
                return $this->redirectToRoute('app_reservation', ['hotelId' => $hotelId, 'chambreId' => $chambreId]);
            }

            if ($dateDebut >= $dateFin) {
                $this->addFlash('error', 'La date de fin doit être postérieure à la date de début.');
                return $this->redirectToRoute('app_reservation', ['hotelId' => $hotelId, 'chambreId' => $chambreId]);
            }

            $reservation = new ReservationHotel();
            $reservation->setChambreId($chambre->getChambreId());
            $reservation->setClientId(1);
            $reservation->setDateDebut($dateDebut);
            $reservation->setDateFin($dateFin);
            $reservation->setStatusEnu('en attente');
            $reservation->setPrixTotale($chambre->getPrixParNuit() * $dateDebut->diff($dateFin)->days);

            $reservationRepository->add($reservation);

            $this->addFlash('success', 'Réservation effectuée avec succès.');
            return $this->redirectToRoute('app_reservation', ['hotelId' => $hotelId, 'chambreId' => $chambreId]);
        }

        $this->addFlash('info', 'Veuillez remplir le formulaire pour réserver.');

        return $this->render('reservationchambre/index.html.twig', [
            'chambre' => $chambre,
            'hotelId' => $hotelId,
            'chambreId' => $chambreId
        ]);
    }

    #[Route('/reservation/edit/{id}', name: 'reservation_edit')]
    public function editReservation(int $id, Request $request, ReservationHotelRepository $reservationRepo): Response
    {
        $reservation = $reservationRepo->find($id);
        if (!$reservation) {
            throw $this->createNotFoundException('Réservation non trouvée.');
        }

        if ($request->isMethod('POST')) {
            $reservation->setDateDebut(new \DateTime($request->request->get('date_debut')));
            $reservation->setDateFin(new \DateTime($request->request->get('date_fin')));
            $reservation->setStatusEnu($request->request->get('status_enu'));
            $reservation->setPrixTotale((int)$request->request->get('prix_totale'));

            $reservationRepo->update($reservation);

            return $this->redirectToRoute('app_reservation_list');
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation
        ]);
    }

    #[Route('/reservation/delete/{id}', name: 'reservation_delete')]
    public function deleteReservation(int $id, ReservationHotelRepository $reservationRepo): Response
    {
        $reservation = $reservationRepo->find($id);
        if (!$reservation) {
            throw $this->createNotFoundException('Réservation non trouvée.');
        }

        $reservationRepo->delete($id);

        return $this->redirectToRoute('app_reservation_list');
    }
}
