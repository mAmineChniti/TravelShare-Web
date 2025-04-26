<?php

namespace App\Controller;

use App\Entity\ReservationHotel;
use App\Repository\ChambresRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ReservationHotelRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationHotelController extends AbstractController
{
    #[Route('/room/reserve/{roomId}', name: 'room_reserve', methods: ['POST'])]
    public function reserveRoom(
        int $roomId,
        Request $request,
        ChambresRepository $chambresRepository,
        ReservationHotelRepository $reservationHotelRepository,
    ): Response {
        $room = $chambresRepository->find($roomId);
        if (!$room) {
            return new JsonResponse(['error' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }

        $today = new \DateTime('today');

        $startStr = $request->request->get('startReservation');
        $endStr = $request->request->get('endReservation');

        if (!$startStr || !$endStr) {
            return new JsonResponse(['error' => 'Both start and end dates are required.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $start = new \DateTime($startStr);
            $end = new \DateTime($endStr);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format.'], Response::HTTP_BAD_REQUEST);
        }
        if ($start < $today) {
            return new JsonResponse(['error' => 'Start date cannot be in the past.'], Response::HTTP_BAD_REQUEST);
        }

        if ($end <= $start) {
            return new JsonResponse(['error' => 'End date must be after the start date.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $duration = $end->diff($start)->days;
            $reservation = new ReservationHotel();
            $reservation->setClientId(1);
            $reservation->setChambreId($roomId);
            $reservation->setDateDebut($start);
            $reservation->setDateFin($end);
            $reservation->setStatusEnu('');
            $reservation->setPrixTotale($room->getPrixParNuit() * $duration);

            $reservationHotelRepository->add($reservation);

            $this->addFlash('success', 'Room reserved successfully!');

            return $this->redirectToRoute('room_details', ['roomId' => $roomId]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while reserving the room: '.$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
