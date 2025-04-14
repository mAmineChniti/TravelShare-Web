<?php

namespace App\Controller;

use App\Entity\Chambres;
use App\Entity\Hotels;
use App\Repository\HotelsRepository;  // Correction ici
use App\Repository\ChambresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChambresController extends AbstractController
{
    #[Route('/hotel/{id}/chambres', name: 'chambres_by_hotel')]
    public function chambresByHotel(int $id, HotelsRepository $hotelRepo, ChambresRepository $chambresRepo): Response
{
    $hotel = $hotelRepo->find($id);
    if (!$hotel) {
        throw $this->createNotFoundException('Hôtel non trouvé.');
    }

    $chambresDisponibles = $chambresRepo->findBy([
        'hotel' => $hotel, 
        'disponible' => true,
    ]);

    return $this->render('chambres/index.html.twig', [
        'hotel' => $hotel,
        'chambres' => $chambresDisponibles,
    ]);
}

    

    #[Route('/chambre/add/{hotelId}', name: 'chambre_add')]
    public function addChambre(int $hotelId, Request $request, EntityManagerInterface $em): Response
    {
        $hotel = $em->getRepository(Hotels::class)->find($hotelId);

        if (!$hotel) {
            throw $this->createNotFoundException('Hôtel non trouvé');
        }

        if ($request->isMethod('POST')) {
            $chambre = new Chambres();
            $chambre->setHotel($hotel);
            $chambre->setNumeroChambre($request->request->get('numero_chambre'));
            $chambre->setTypeEnu($request->request->get('type_enu'));
            $chambre->setPrixParNuit((float)$request->request->get('prix_par_nuit'));
            $chambre->setDisponible($request->request->get('disponible') === '1');

            $em->persist($chambre);
            $em->flush();

            return $this->redirectToRoute('chambres_by_hotel', ['id' => $hotelId]);
        }

        return $this->render('chambres/add.html.twig', [
            'hotel' => $hotel
        ]);
    }

    #[Route('/chambre/edit/{id}', name: 'chambre_edit')]
    public function editChambre(Chambres $chambre, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $chambre->setNumeroChambre($request->request->get('numero_chambre'));
            $chambre->setTypeEnu($request->request->get('type_enu'));
            $chambre->setPrixParNuit((float)$request->request->get('prix_par_nuit'));
            $chambre->setDisponible($request->request->get('disponible') === '1');

            $em->flush();

            return $this->redirectToRoute('chambres_by_hotel', [
                'id' => $chambre->getHotel()->getId()
            ]);
        }

        return $this->render('chambres/edit.html.twig', [
            'chambre' => $chambre
        ]);
    }

    #[Route('/chambre/delete/{id}', name: 'chambre_delete')]
    public function deleteChambre(Chambres $chambre, EntityManagerInterface $em): Response
    {
        $hotelId = $chambre->getHotel()->getId();

        $em->remove($chambre);
        $em->flush();

        return $this->redirectToRoute('chambres_by_hotel', ['id' => $hotelId]);
    }
}
