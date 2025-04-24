<?php

namespace App\Controller;

use App\Entity\Hotels;
use App\Repository\HotelsRepository;
use App\Repository\ChambresRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function addHotel(Request $request, HotelsRepository $hotelsRepository, ValidatorInterface $validator): Response
    {
        $hotel = new Hotels();
        $errorMessages = [];
        if ($request->isMethod('POST')) {
            $hotel->setNom($request->request->get('nom'));
            $hotel->setAdress($request->request->get('adress'));
            $hotel->setTelephone($request->request->get('telephone'));
            $hotel->setCapaciteTotale((int) $request->request->get('capaciteTotale'));

            $imageFile = $request->files->get('imageH');
            if ($imageFile && $imageFile->isValid()) {
                $imageData = fopen($imageFile->getPathname(), 'rb');
                $hotel->setImageH($imageData);
            } else {
                $errorMessages[] = 'Hotel Image is required and must be valid.';
            }

            $errors = $validator->validate($hotel);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
            } else {
                $hotelsRepository->add($hotel);
                $this->addFlash('success', 'Hotel added successfully!');

                return $this->redirectToRoute('dashboard_hotels');
            }
        }

        return $this->render('dashboard/hotels/add.html.twig', [
            'hotel' => $hotel,
            'errorMessages' => $errorMessages,
        ]);
    }

    #[Route('/dashboard/hotels/edit/{id}', name: 'dashboard_hotels_edit', methods: ['GET', 'POST'])]
    public function editHotel(int $id, Request $request, HotelsRepository $hotelsRepository, ValidatorInterface $validator): Response
    {
        $hotel = $hotelsRepository->find($id);
        if (!$hotel) {
            return $this->redirectToRoute('dashboard_hotels');
        }
        if ($hotel->getImageH()) {
            $hotel->base64Image = base64_encode(stream_get_contents($hotel->getImageH()));
        } else {
            $hotel->base64Image = null;
        }
        $errorMessages = [];
        if ($request->isMethod('POST')) {
            $hotel->setNom($request->request->get('nom'));
            $hotel->setAdress($request->request->get('adress'));
            $hotel->setTelephone($request->request->get('telephone'));
            $hotel->setCapaciteTotale((int) $request->request->get('capaciteTotale'));

            $imageFile = $request->files->get('imageH');
            if ($imageFile && $imageFile->isValid()) {
                $imageData = fopen($imageFile->getPathname(), 'rb');
                $hotel->setImageH($imageData);
            } else {
                $errorMessages[] = 'Hotel Image is required and must be valid.';
            }

            $errors = $validator->validate($hotel);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
            } else {
                $hotelsRepository->update($hotel);
                $this->addFlash('success', 'Hotel updated successfully!');

                return $this->redirectToRoute('dashboard_hotels');
            }
        }

        return $this->render('dashboard/hotels/edit.html.twig', [
            'hotel' => $hotel,
            'errorMessages' => $errorMessages,
        ]);
    }

    #[Route('/dashboard/hotels/delete/{id}', name: 'dashboard_hotels_delete')]
    public function deleteHotel(int $id, HotelsRepository $hotelsRepository): Response
    {
        $hotel = $hotelsRepository->find($id);

        if (!$hotel) {
            $this->addFlash('error', 'Hotel not found.');

            return $this->redirectToRoute('dashboard_hotels');
        }

        $hotelsRepository->delete($id);
        $this->addFlash('success', 'Hotel deleted successfully!');

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
