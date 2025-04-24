<?php

namespace App\Controller;

use App\Entity\OffresVoyage;
use App\Entity\ReservationOffresVoyage;
use App\Repository\OffresVoyageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class FlightsController extends AbstractController
{
    #[Route('/flights', name: 'app_flights')]
    public function index(OffresVoyageRepository $voyageService): Response
    {
        $voyages = $voyageService->findAllOffres();

        return $this->render('flights/index.html.twig', [
            'voyages' => $voyages,
        ]);
    }

    #[Route('/flights/{id}', name: 'app_flight_details')]
    public function details(int $id, OffresVoyageRepository $voyageService): Response
    {
        $voyage = $voyageService->find($id);

        if (!$voyage) {
            throw $this->createNotFoundException('The flight does not exist');
        }

        return $this->render('flights/details.html.twig', [
            'voyage' => $voyage,
        ]);
    }

    #[Route('/flights/{id}/reserve', name: 'app_reserve_flight', methods: ['POST'])]
    public function reserveFlight(int $id, Request $request, OffresVoyageRepository $voyageService, ValidatorInterface $validator): Response
    {
        $voyage = $voyageService->find($id);

        if (!$voyage) {
            $this->addFlash('error', 'Flight not found.');

            return $this->redirectToRoute('app_flights');
        }

        $nbrPlace = (int) $request->request->get('nbrPlace');

        if ($nbrPlace < 1 || $nbrPlace > $voyage->getPlacesDisponibles()) {
            $this->addFlash('error', 'Invalid number of seats selected.');

            return $this->redirectToRoute('app_flight_details', ['id' => $id]);
        }

        $reservation = new ReservationOffresVoyage();
        $reservation->setOffreId($voyage->getOffresVoyageId());
        $reservation->setNbrPlace($nbrPlace);
        $reservation->setClientId(1);

        $errors = $validator->validate($reservation);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }

            return $this->redirectToRoute('app_flight_details', ['id' => $id]);
        }

        $voyageService->reserve($reservation);
        $this->addFlash('success', "You have successfully reserved $nbrPlace seat(s). Have fun on your flight!");

        return $this->redirectToRoute('app_flight_details', ['id' => $id]);
    }

    #[Route('/dashboard/flights', name: 'app_dashboard_flights')]
    public function dashboardFlights(OffresVoyageRepository $voyageService, Request $request): Response
    {
        $voyages = $voyageService->findAllOffres();

        return $this->render('dashboard/flights/index.html.twig', [
            'voyages' => $voyages,
        ]);
    }

    #[Route('/dashboard/flights/add', name: 'app_add_flight')]
    public function addFlight(Request $request, OffresVoyageRepository $voyageService, ValidatorInterface $validator): Response
    {
        $errorMessages = [];
        if ($request->isMethod('POST')) {
            $voyage = new OffresVoyage();
            $voyage->setTitre($request->request->get('titre'));
            $voyage->setDestination($request->request->get('destination'));
            $voyage->setDescription($request->request->get('description'));
            $voyage->setPrix((float) $request->request->get('prix'));
            $voyage->setPlacesDisponibles((int) $request->request->get('placesDisponibles'));
            $voyage->setDateDepart(new \DateTime($request->request->get('dateDepart')));
            $voyage->setDateRetour(new \DateTime($request->request->get('dateRetour')));

            $errors = $validator->validate($voyage);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
            }

            $voyageService->add($voyage);
            $this->addFlash('success', 'Flight added successfully!');

            return $this->redirectToRoute('app_dashboard_flights');
        }

        return $this->render('dashboard/flights/add.html.twig', [
            'errorMessages' => $errorMessages,
        ]);
    }

    #[Route('/dashboard/flights/{id}/edit', name: 'app_edit_flight')]
    public function editFlight(int $id, Request $request, OffresVoyageRepository $voyageService, ValidatorInterface $validator): Response
    {
        $voyage = $voyageService->find($id);

        if (!$voyage) {
            return new JsonResponse(['error' => 'Flight not found'], Response::HTTP_NOT_FOUND);
        }

        $errorMessages = [];
        if ($request->isMethod('POST')) {
            $voyage->setTitre($request->request->get('titre'));
            $voyage->setDestination($request->request->get('destination'));
            $voyage->setDescription($request->request->get('description'));
            $voyage->setPrix((float) $request->request->get('prix'));
            $voyage->setPlacesDisponibles((int) $request->request->get('placesDisponibles'));
            $voyage->setDateDepart(new \DateTime($request->request->get('dateDepart')));
            $voyage->setDateRetour(new \DateTime($request->request->get('dateRetour')));

            $errors = $validator->validate($voyage);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
            }

            $voyageService->update($voyage);
            $this->addFlash('success', 'Flight updated successfully!');

            return $this->redirectToRoute('app_dashboard_flights');
        }

        return $this->render('dashboard/flights/edit.html.twig', [
            'voyage' => $voyage,
            'errorMessages' => $errorMessages,
        ]);
    }

    #[Route('/dashboard/flights/{id}/delete', name: 'app_delete_flight', methods: ['POST'])]
    public function deleteFlight(int $id, OffresVoyageRepository $voyageService): Response
    {
        $voyage = $voyageService->find($id);

        if (!$voyage) {
            $this->addFlash('error', 'Flight not found.');

            return $this->redirectToRoute('app_dashboard_flights');
        }

        $voyageService->delete($voyage);
        $this->addFlash('success', 'Flight deleted successfully!');

        return $this->redirectToRoute('app_dashboard_flights');
    }
}
