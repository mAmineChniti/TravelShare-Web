<?php

namespace App\Controller;

use App\Repository\HotelsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Hotels;
use App\Repository\ChambresRepository;

class HotelsController extends AbstractController
{
    #[Route('/hotels', name: 'app_hotels')]
    public function index(HotelsRepository $hotelsRepository): Response
    {
        $hotels = $hotelsRepository->findAll();
        foreach ($hotels as $hotel) {
            if ($hotel->getImageH()) {
                $hotel->base64Image = base64_encode(stream_get_contents($hotel->getImageH()));
            } else {
                $hotel->base64Image = null;
            }
        }

        return $this->render('hotels/index.html.twig', [
            'hotels' => $hotels,
        ]);
    }

    #[Route('/dashboard/hotels', name: 'dashboard_hotels')]
    public function dashboardHotels(HotelsRepository $hotelsRepository): Response
    {
        $hotels = $hotelsRepository->findAll();
        foreach ($hotels as $hotel) {
            if ($hotel->getImageH()) {
                $hotel->base64Image = base64_encode(stream_get_contents($hotel->getImageH()));
            } else {
                $hotel->base64Image = null;
            }
        }

        return $this->render('dashboard/hotels/index.html.twig', [
            'hotels' => $hotels,
        ]);
    }

    #[Route('/dashboard/hotels/add', name: 'dashboard_hotels_add', methods: ['GET', 'POST'])]
    public function addHotel(Request $request, HotelsRepository $hotelsRepository): Response
    {
        $hotel = new Hotels();

        if ($request->isMethod('POST')) {
            try {
                $hotel->setNom($request->request->get('nom'));
                $hotel->setAdress($request->request->get('adress'));
                $hotel->setTelephone($request->request->get('telephone'));
                $hotel->setCapaciteTotale((int)$request->request->get('capaciteTotale'));

                $imageFile = $request->files->get('imageH');
                if ($imageFile && $imageFile->isValid()) {
                    $imageData = $imageFile->getContent();
                    $hotel->setImageH($imageData);
                }

                $hotelsRepository->add($hotel);
                $this->addFlash('success', 'Hotel added successfully!');

                return $this->redirectToRoute('dashboard_hotels');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute("dashboard_hotels_add");
            }
        }

        return $this->render('dashboard/hotels/add.html.twig', [
            'hotel' => $hotel,
        ]);
    }

    #[Route('/dashboard/hotels/edit/{id}', name: 'dashboard_hotels_edit', methods: ['GET', 'POST'])]
    public function editHotel(int $id, Request $request, HotelsRepository $hotelsRepository): Response
    {
        $hotel = $hotelsRepository->find($id);
        if (!$hotel) {
            throw $this->createNotFoundException('Hotel not found');
        }
        $hotel->base64Image = base64_encode(stream_get_contents($hotel->getImageH()));

        if ($request->isMethod('POST')) {
            try {
                $hotel->setNom($request->request->get('nom'));
                $hotel->setAdress($request->request->get('adress'));
                $hotel->setTelephone($request->request->get('telephone'));
                $hotel->setCapaciteTotale((int)$request->request->get('capaciteTotale'));

                $imageFile = $request->files->get('imageH');
                if ($imageFile && $imageFile->isValid()) {
                    $imageData = $imageFile->getContent();
                    $hotel->setImageH($imageData);
                }

                $hotelsRepository->update($hotel);
                $this->addFlash('success', 'Hotel updated successfully!');

                return $this->redirectToRoute('dashboard_hotels');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute("dashboard_hotels_edit", [
                    'id'=>$id
                ]);
            }
        }

        return $this->render('dashboard/hotels/edit.html.twig', [
            'hotel' => $hotel,
        ]);
    }

    #[Route('/dashboard/hotels/delete/{id}', name: 'dashboard_hotels_delete')]
    public function deleteHotel(int $id, HotelsRepository $hotelsRepository): Response
    {
        $hotelsRepository->delete($id);
        return $this->redirectToRoute('dashboard_hotels');
    }

    #[Route('/hotels/{hotelId}/rooms', name: 'hotel_rooms')]
    public function viewRooms(int $hotelId, HotelsRepository $hotelsRepository, ChambresRepository $chambresRepository): Response
    {
        $hotel = $hotelsRepository->find($hotelId);
        if (!$hotel) {
            throw $this->createNotFoundException('Hotel not found');
        }
        $rooms = $chambresRepository->findBy(['hotel' => $hotelId]);
        return $this->render('hotels/rooms.html.twig', [
            'hotel' => $hotel,
            'rooms' => $rooms,
        ]);
    }
}
