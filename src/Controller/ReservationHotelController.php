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
    #[Route('/room/reserve/{roomId}', name: 'room_reserve', methods: ['POST'])]
    public function reserveRoom(
        int $roomId,
        Request $request,
        ChambresRepository $chambresRepository,
        ReservationHotelRepository $reservationHotelRepository
    ): Response {
        $room = $chambresRepository->find($roomId);
        if (!$room) {
            throw $this->createNotFoundException('Room not found');
        }

        $today = new \DateTime('today');

        $startStr = $request->request->get('startReservation');
        $endStr   = $request->request->get('endReservation');

        if (!$startStr || !$endStr) {
            $this->addFlash('error', 'Please provide both start and end dates for the reservation.');
            return $this->redirectToRoute('room_details', ['roomId' => $roomId]);
        }

        try {
            $start = new \DateTime($startStr);
            $end   = new \DateTime($endStr);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Invalid date format.');
            return $this->redirectToRoute('room_details', ['roomId' => $roomId]);
        }

        // 1) Start date cannot be in the past
        if ($start < $today) {
            $this->addFlash('error', 'Start date cannot be in the past.');
            return $this->redirectToRoute('room_details', ['roomId' => $roomId]);
        }

        // 2) End date cannot be in the past
        if ($end < $today) {
            $this->addFlash('error', 'End date cannot be in the past.');
            return $this->redirectToRoute('room_details', ['roomId' => $roomId]);
        }

        // 3) Start must be before end
        if ($start >= $end) {
            $this->addFlash('error', 'Start date must be before end date.');
            return $this->redirectToRoute('room_details', ['roomId' => $roomId]);
        }

        try {
            $duration = $end->diff($start)->days;
            $reservation = new ReservationHotel();
            $reservation->setClientId(1);
            $reservation->setChambreId($roomId);
            $reservation->setDateDebut($start);
            $reservation->setDateFin($end);
            $reservation->setStatusEnu('Pending');
            $reservation->setPrixTotale($room->getPrixParNuit() * $duration);

            $reservationHotelRepository->add($reservation);

            $this->addFlash('success', 'Room reserved successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred while reserving the room: ' . $e->getMessage());
        }

        return $this->redirectToRoute('room_details', ['roomId' => $roomId]);
    }
}
