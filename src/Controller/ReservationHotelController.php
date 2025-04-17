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
use Symfony\Component\Validator\Constraints\Date;

class ReservationHotelController extends AbstractController
{
    #[Route('/room/reserve/{roomId}', name: 'room_reserve', methods: ['POST'])]
    public function reserveRoom(int $roomId, Request $request, ChambresRepository $chambresRepository, ReservationHotelRepository $reservationHotelRepository): Response
    {
        $room = $chambresRepository->find($roomId);
        $today = new Date();
        if (!$room) {
            throw $this->createNotFoundException('Room not found');
        }

        $startReservation = $request->request->get('startReservation');
        $endReservation = $request->request->get('endReservation');

        if (!$startReservation || !$endReservation) {
            $this->addFlash('error', 'Please provide both start and end dates for the reservation.');
            return $this->redirectToRoute('room_details', ['roomId' => $roomId]);
        }

        if (new \DateTime($startReservation) < $today) {
            $this->addFlash('error', 'Start date cannot be in the past.');
            return $this->redirectToRoute('room_details', ['roomId' => $roomId]);
        }

        if (new \DateTime($endReservation) < $today) {
            $this->addFlash('error', 'End date cannot be in the past.');
            return $this->redirectToRoute('room_details', ['roomId' => $roomId]);
        }

        if (new \DateTime($startReservation) >= new \DateTime($endReservation)) {
            $this->addFlash('error', 'Start date must be before end date.');
            return $this->redirectToRoute('room_details', ['roomId' => $roomId]);
        }

        try {
            $reservation = new ReservationHotel();
            $reservation->setClientId(1);
            $reservation->setChambreId($roomId);
            $reservation->setDateDebut(new \DateTime($startReservation));
            $reservation->setDateFin(new \DateTime($endReservation));
            $reservation->setStatusEnu('Pending');
            $reservation->setPrixTotale($room->getPrixParNuit() * (new \DateTime($endReservation))->diff(new \DateTime($startReservation))->days);

            $reservationHotelRepository->add($reservation);

            $this->addFlash('success', 'Room reserved successfully!');
            return $this->redirectToRoute('room_details', ['roomId' => $roomId]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred while reserving the room: ' . $e->getMessage());
            return $this->redirectToRoute('room_details', ['roomId' => $roomId]);
        }
    }
}
