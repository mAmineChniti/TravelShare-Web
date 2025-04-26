<?php

namespace App\Controller;

use App\Entity\Chambres;
use App\Repository\HotelsRepository;
use App\Repository\ChambresRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function addRoom(Request $request, ChambresRepository $chambresRepository, HotelsRepository $hotelsRepository, ValidatorInterface $validator, int $hotelId): Response
    {
        $hotel = $hotelsRepository->find($hotelId);
        if (!$hotel) {
            $this->addFlash('error', 'Hotel not found.');

            return $this->redirectToRoute('dashboard_rooms', ['id' => $hotelId]);
        }

        $room = new Chambres();
        if ($request->isMethod('POST')) {
            $room->setNumeroChambre($request->request->get('numeroChambre'));
            $room->setTypeEnu($request->request->get('typeEnu'));
            $room->setPrixParNuit((float) $request->request->get('prixParNuit'));
            $room->setDisponible((bool) $request->request->get('disponible'));
            $room->setHotel($hotel);

            $errors = $validator->validate($room);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }

                return $this->render('dashboard/rooms/add.html.twig', [
                    'room' => $room,
                    'hotelId' => $hotelId,
                    'errorMessages' => $errorMessages,
                ]);
            }

            $chambresRepository->add($room);
            $this->addFlash('success', 'Room added successfully!');

            return $this->redirectToRoute('dashboard_rooms', ['id' => $hotelId]);
        }

        return $this->render('dashboard/rooms/add.html.twig', [
            'room' => $room,
            'hotelId' => $hotelId,
        ]);
    }

    #[Route('/dashboard/rooms/edit/{id}', name: 'dashboard_rooms_edit', methods: ['GET', 'POST'])]
    public function editRoom(int $id, Request $request, ChambresRepository $chambresRepository, ValidatorInterface $validator): Response
    {
        $room = $chambresRepository->find($id);
        if (!$room) {
            $this->addFlash('error', 'Room not found.');

            return $this->redirectToRoute('dashboard_hotels');
        }

        if ($request->isMethod('POST')) {
            $room->setNumeroChambre($request->request->get('numeroChambre'));
            $room->setTypeEnu($request->request->get('typeEnu'));
            $room->setPrixParNuit((float) $request->request->get('prixParNuit'));
            $room->setDisponible((bool) $request->request->get('disponible'));

            $errors = $validator->validate($room);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }

                return $this->render('dashboard/rooms/edit.html.twig', [
                    'room' => $room,
                    'errorMessages' => $errorMessages,
                ]);
            }

            $chambresRepository->update($room);
            $this->addFlash('success', 'Room updated successfully!');

            return $this->redirectToRoute('dashboard_rooms', ['id' => $room->getHotel()->getId()]);
        }

        return $this->render('dashboard/rooms/edit.html.twig', [
            'room' => $room,
        ]);
    }

    #[Route('/dashboard/rooms/delete/{id}', name: 'dashboard_rooms_delete', methods: ['POST'])]
    public function deleteRoom(int $id, ChambresRepository $chambresRepository): Response
    {
        $room = $chambresRepository->find($id);
        if (!$room) {
            $this->addFlash('error', 'Room not found.');

            return $this->redirectToRoute('dashboard_hotels');
        }

        $hotelId = $room->getHotel()->getId();
        $chambresRepository->delete($id);
        $this->addFlash('success', sprintf('Room #%s has been successfully deleted.', $room->getNumeroChambre()));

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
