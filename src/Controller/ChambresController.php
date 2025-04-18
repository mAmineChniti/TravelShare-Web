<?php

namespace App\Controller;

use App\Entity\Chambres;
use App\Entity\Hotels;
use App\Repository\HotelsRepository;
use App\Repository\ChambresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChambresController extends AbstractController
{
    #[Route('/dashboard/rooms/{id}', name: 'dashboard_rooms')]
    public function dashboardRooms(ChambresRepository $chambresRepository, int $id): Response
    {
        $chambres = $chambresRepository->findBy(['hotel' => $id]);

        return $this->render('dashboard/rooms/index.html.twig', [
            'hotelId' => $id,
            'chambres' => $chambres,
        ]);
    }

    #[Route('/dashboard/rooms/add/{hotelId}', name: 'dashboard_rooms_add', methods: ['GET', 'POST'])]
    public function addRoom(Request $request, ChambresRepository $chambresRepository, HotelsRepository $hotelsRepository, int $hotelId): Response
    {
        $room = new Chambres();
        $hotel = $hotelsRepository->find($hotelId);

        if (!$hotel) {
            throw $this->createNotFoundException('Hotel not found');
        }

        if ($request->isMethod('POST')) {
            try {
                $room->setNumeroChambre($request->request->get('numeroChambre'));
                $room->setTypeEnu($request->request->get('typeEnu'));
                $room->setPrixParNuit((float)$request->request->get('prixParNuit'));
                $room->setDisponible((bool)$request->request->get('disponible'));
                $room->setHotel($hotel);

                $chambresRepository->add($room);
                $this->addFlash('success', sprintf('Room #%s has been successfully added to Hotel #%s.', $room->getNumeroChambre(), $hotel->getId()));

                return $this->redirectToRoute('dashboard_rooms', ['id' => $hotel->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('dashboard_rooms_add', ['hotelId' => $hotelId]);
            }
        }

        return $this->render('dashboard/rooms/add.html.twig', [
            'room' => $room,
            'hotelId' => $hotelId,
        ]);
    }

    #[Route('/dashboard/rooms/edit/{id}', name: 'dashboard_rooms_edit', methods: ['GET', 'POST'])]
    public function editRoom(int $id, Request $request, ChambresRepository $chambresRepository): Response
    {
        $room = $chambresRepository->find($id);
        if (!$room) {
            throw $this->createNotFoundException('Room not found');
        }

        if ($request->isMethod('POST')) {
            try {
                $room->setNumeroChambre($request->request->get('numeroChambre'));
                $room->setTypeEnu($request->request->get('typeEnu'));
                $room->setPrixParNuit((float)$request->request->get('prixParNuit'));
                $room->setDisponible((bool)$request->request->get('disponible'));

                $chambresRepository->update($room);
                $this->addFlash('success', 'Room updated successfully!');

                return $this->redirectToRoute('dashboard_rooms', ['id' => $room->getHotel()->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('dashboard_rooms_edit', ['id' => $id]);
            }
        }

        return $this->render('dashboard/rooms/edit.html.twig', [
            'room' => $room,
        ]);
    }

    #[Route('/dashboard/rooms/delete/{id}', name: 'dashboard_rooms_delete')]
    public function deleteRoom(int $id, ChambresRepository $chambresRepository): Response
    {
        $room = $chambresRepository->find($id);
        if (!$room) {
            throw $this->createNotFoundException('Room not found');
        }

        $hotelId = $room->getHotel()->getId();
        $chambresRepository->delete($id);

        $this->addFlash('success', sprintf('Room #%s has been successfully deleted from Hotel #%s.', $room->getNumeroChambre(), $hotelId));

        return $this->redirectToRoute('dashboard_rooms', ['id' => $hotelId]);
    }

    #[Route('/room/{roomId}', name: 'room_details')]
    public function roomDetails(int $roomId, ChambresRepository $chambresRepository): Response
    {
        $room = $chambresRepository->find($roomId);

        if (!$room) {
            throw $this->createNotFoundException('Room not found');
        }

        return $this->render('rooms/details.html.twig', [
            'room' => $room,
        ]);
    }
}
