<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OffresVoyageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\ReservationOffresVoyage;
use App\Repository\ReservationOffresVoyageRepository;
use App\Entity\OffresVoyage;

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
    public function reserve(int $id, Request $request, OffresVoyageRepository $voyageRespository, ReservationOffresVoyageRepository $reservationService): RedirectResponse
    {
        $voyage = $voyageRespository->find($id);

        if (!$voyage) {
            throw $this->createNotFoundException('The flight does not exist');
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
        try {
            $reservationService->add($reservation);
            $this->addFlash('success', "You have successfully reserved $nbrPlace seat(s). Have fun on your flight!");
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_flight_details', ['id' => $id]);
    }

    #[Route('/dashboard/flights', name: 'app_dashboard_flights')]
    public function dashboardFlights(OffresVoyageRepository $voyageService, Request $request): Response
    {
        $voyages = $voyageService->findAllOffres();

        return $this->render('dashboard/flights.html.twig', [
            'voyages' => $voyages,
        ]);
    }

    #[Route('/dashboard/flights/add', name: 'app_add_flight')]
    public function addFlight(Request $request, OffresVoyageRepository $voyageService): Response
    {
        if ($request->isMethod('POST')) {
            try {
                $titre = $request->request->get('titre');
                $destination = $request->request->get('destination');
                $description = $request->request->get('description');
                $prix = (float) $request->request->get('prix');
                $placesDisponibles = (int) $request->request->get('placesDisponibles');
                $dateDepart = new \DateTime($request->request->get('dateDepart'));
                $dateRetour = new \DateTime($request->request->get('dateRetour'));

                $voyage = new OffresVoyage();
                $voyage->setTitre($titre);
                $voyage->setDestination($destination);
                $voyage->setDescription($description);
                $voyage->setPrix($prix);
                $voyage->setPlacesDisponibles($placesDisponibles);
                $voyage->setDateDepart($dateDepart);
                $voyage->setDateRetour($dateRetour);

                $voyageService->add($voyage);

                $this->addFlash('success', 'Flight added successfully!');
                return $this->redirectToRoute('app_dashboard_flights');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('app_add_flight');
            }
        }

        return $this->render('dashboard/add_flight.html.twig');
    }

    #[Route('/dashboard/flights/{id}/edit', name: 'app_edit_flight')]
    public function editFlight(int $id, Request $request, OffresVoyageRepository $voyageService): Response
    {
        $voyage = $voyageService->find($id);

        if (!$voyage) {
            throw $this->createNotFoundException('Flight not found');
        }

        if ($request->isMethod('POST')) {
            try {
                $voyage->setTitre($request->request->get('titre'));
                $voyage->setDestination($request->request->get('destination'));
                $voyage->setDescription($request->request->get('description'));
                $voyage->setPrix((float) $request->request->get('prix'));
                $voyage->setPlacesDisponibles((int) $request->request->get('placesDisponibles'));
                $voyage->setDateDepart(new \DateTime($request->request->get('dateDepart')));
                $voyage->setDateRetour(new \DateTime($request->request->get('dateRetour')));

                $voyageService->update($voyage);

                $this->addFlash('success', 'Flight updated successfully!');
                return $this->redirectToRoute('app_dashboard_flights');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('app_edit_flight', ['id' => $id]);
            }
        }

        return $this->render('dashboard/edit_flight.html.twig', [
            'voyage' => $voyage,
        ]);
    }

    #[Route('/dashboard/flights/{id}/delete', name: 'app_delete_flight', methods: ['POST'])]
    public function deleteFlight(int $id, OffresVoyageRepository $voyageService): RedirectResponse
    {
        $voyage = $voyageService->find($id);

        if (!$voyage) {
            throw $this->createNotFoundException('Flight not found');
            return $this->redirectToRoute('app_dashboard_flights');
        }

        $voyageService->delete($voyage);

        $this->addFlash('success', 'Flight deleted successfully!');
        return $this->redirectToRoute('app_dashboard_flights');
    }
}
