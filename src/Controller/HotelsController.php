<?php

namespace App\Controller;

use App\Entity\Hotels;
use App\Repository\HotelsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HotelsController extends AbstractController
{
    #[Route('/hotels', name: 'app_hotels')]
    public function listHotels(HotelsRepository $hotelsRepository): Response
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

    #[Route('/hotel/{id}', name: 'hotel_show')]
    public function showHotel(Hotels $hotel): Response
    {
        return $this->render('hotels/show.html.twig', [
            'hotel' => $hotel,
        ]);
    }

    #[Route('/hotel/add', name: 'hotel_add')]
    public function addHotel(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_hotels');
        }

        if ($request->isMethod('POST')) {
            $hotel = new Hotels();
            $hotel->setNom($request->request->get('nom'));
            $hotel->setAdress($request->request->get('adress'));
            $hotel->setTelephone($request->request->get('telephone'));
            $hotel->setCapaciteTotale((int) $request->request->get('capacite_totale'));

            $entityManager->persist($hotel);
            $entityManager->flush();

            return $this->redirectToRoute('app_hotels');
        }

        return $this->render('hotels/add.html.twig');
    }

    #[Route('/hotel/edit/{id}', name: 'hotel_edit')]
    public function editHotel(Hotels $hotel, Request $request, EntityManagerInterface $entityManager): Response
    {
        /**if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('hotel_list');
        }**/

        if ($request->isMethod('POST')) {
            $hotel->setNom($request->request->get('nom'));
            $hotel->setAdress($request->request->get('adress'));
            $hotel->setTelephone($request->request->get('telephone'));
            $hotel->setCapaciteTotale((int) $request->request->get('capacite_totale'));

            $entityManager->flush();
            return $this->redirectToRoute('app_hotels');
        }

        return $this->render('hotels/edit.html.twig', [
            'hotel' => $hotel,
        ]);
    }

    #[Route('/hotel/delete/{id}', name: 'hotel_delete')]
    public function deleteHotel(Hotels $hotel, EntityManagerInterface $entityManager): Response
    {
       /** if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('hotel_list');
        }**/

        $entityManager->remove($hotel);
        $entityManager->flush();

        return $this->redirectToRoute('app_hotels');
    }
}
