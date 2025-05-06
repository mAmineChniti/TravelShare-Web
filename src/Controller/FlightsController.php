<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use App\Entity\OffresVoyage;
use App\Entity\ReservationOffresVoyage;
use App\Repository\OffresVoyageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ReservationOffresVoyageRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class FlightsController extends AbstractController
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/flights', name: 'app_flights')]
    public function index(OffresVoyageRepository $voyageService): Response
    {
        $voyages = $voyageService->findAllOffres();
        $voyages = array_filter($voyages, function ($voyage) {
            $departureDate = $voyage->getDateDepart();
            $today = new \DateTime('today');

            return $departureDate > $today;
        });

        return $this->render('flights/index.html.twig', [
            'voyages' => $voyages,
        ]);
    }

    #[Route('/flights/{id}', name: 'app_flight_details', requirements: ['id' => '\\d+'])]
    public function details(int $id, OffresVoyageRepository $voyageService, Request $request): Response
    {
        $voyage = $voyageService->find($id);

        if (!$voyage) {
            $this->addFlash('error', 'Flight not found.');

            return $this->redirectToRoute('app_flights');
        }

        $reservationId = $request->query->get('reservationId');

        return $this->render('flights/details.html.twig', [
            'voyage' => $voyage,
            'reservationId' => $reservationId,
        ]);
    }

    #[Route('/flights/{id}/reserve', name: 'app_reserve_flight', methods: ['POST'])]
    public function reserveFlight(int $id, Request $request, OffresVoyageRepository $VoyageOffre, ReservationOffresVoyageRepository $voyageService, ValidatorInterface $validator): Response
    {
        $voyage = $VoyageOffre->find($id);

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
        $reservation->setDateReserved(new \DateTime('today'));
        $reservation->setStatus(1);
        $reservation->setPrix($voyage->getPrix() * $nbrPlace);

        $errors = $validator->validate($reservation);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }

            return $this->redirectToRoute('app_flight_details', ['id' => $id]);
        }

        $promoCode = $request->request->get('promoCode', '');
        try {
            $reservationId = $voyageService->add($reservation, $promoCode);
            $this->addFlash('success', "You have successfully reserved $nbrPlace seat(s). Have fun on your flight!");

            return $this->redirectToRoute('app_flight_details', ['id' => $reservation->getOffreId(), 'reservationId' => $reservationId]);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('app_flight_details', ['id' => $id]);
        }

        return $this->redirectToRoute('app_flight_details', ['id' => $id]);
    }

    #[Route('/dashboard/flights', name: 'app_dashboard_flights')]
    public function dashboardFlights(OffresVoyageRepository $voyageService): Response
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

    #[Route('/flights/search', name: 'app_flights_search', methods: ['GET'])]
    public function searchByDestination(Request $request, OffresVoyageRepository $voyageService): Response
    {
        $destination = $request->query->get('destination', '');

        if (empty($destination)) {
            $this->addFlash('error', 'Please provide a destination to search for.');

            return $this->redirectToRoute('app_flights');
        }

        $voyages = $voyageService->findByDestination($destination);
        $voyages = array_filter($voyages, function ($voyage) {
            $departureDate = $voyage->getDateDepart();
            $today = new \DateTime('today');

            return $departureDate > $today;
        });

        return $this->render('flights/index.html.twig', [
            'voyages' => $voyages,
        ]);
    }

    #[Route('/flights/sort', name: 'app_flights_sort', methods: ['GET'])]
    public function sortFlights(Request $request, OffresVoyageRepository $voyageService): Response
    {
        $sortBy = $request->query->get('sortBy', '');

        switch ($sortBy) {
            case 'price_asc':
                $voyages = $voyageService->findBy([], ['prix' => 'ASC']);
                break;
            case 'price_desc':
                $voyages = $voyageService->findBy([], ['prix' => 'DESC']);
                break;
            case 'date':
                $voyages = $voyageService->findBy([], ['dateDepart' => 'ASC']);
                break;
            default:
                $voyages = $voyageService->findAllOffres();
        }

        $voyages = array_filter($voyages, function ($voyage) {
            $departureDate = $voyage->getDateDepart();
            $today = new \DateTime('today');

            return $departureDate > $today;
        });

        return $this->render('flights/index.html.twig', [
            'voyages' => $voyages,
        ]);
    }

    #[Route('/flights/{id}/generate-pdf', name: 'app_generate_pdf', methods: ['POST'])]
    public function generatePdf(
        int $id,
        ReservationOffresVoyageRepository $reservationRepo,
        OffresVoyageRepository $offreVoyageRepo,
        Pdf $pdf,
    ): Response {
        $reservation = $reservationRepo->find($id);
        if (!$reservation) {
            $this->addFlash('error', 'Reservation not found.');

            return $this->redirectToRoute('app_flights');
        }

        $voyage = $offreVoyageRepo->find($reservation->getOffreId());
        if (!$voyage) {
            $this->addFlash('error', 'Flight not found.');

            return $this->redirectToRoute('app_flights');
        }

        $qrCodeData = sprintf(
            'Reservation ID: %s, Title: %s, Destination: %s, Price: %s €, Departure Date: %s, Return Date: %s, Number of Seats: %s, Total Price: %s €',
            $reservation->getReservationId(),
            $voyage->getTitre(),
            $voyage->getDestination(),
            $voyage->getPrix(),
            $voyage->getDateDepart()->format('d-m-Y'),
            $voyage->getDateRetour()->format('d-m-Y'),
            $reservation->getNbrPlace(),
            $reservation->getPrix()
        );

        $qrCodeUrl = sprintf(
            'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=%s',
            urlencode($qrCodeData)
        );
        $html = $this->renderView('flights/pdf.html.twig', [
            'reservation' => $reservation,
            'voyage' => $voyage,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
        $pdfOutput = $pdf->getOutputFromHtml($html);

        return new PdfResponse(
            $pdfOutput,
            'reservation_'.$id.'.pdf',
            'application/pdf',
            HeaderUtils::DISPOSITION_ATTACHMENT
        );
    }

    #[Route('/api/currencies', name: 'api_fetch_currencies', methods: ['GET'])]
    public function fetchCurrencies(): JsonResponse
    {
        $apiKey = $this->getParameter('app.exchange_api_key');
        $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/codes";

        try {
            $response = $this->httpClient->request('GET', $url);
            $statusCode = $response->getStatusCode();

            if (200 !== $statusCode) {
                return new JsonResponse(['error' => 'Failed to fetch currencies'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $data = $response->toArray();

            return new JsonResponse($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/country-currency', name: 'api_fetch_country_currency', methods: ['POST'])]
    public function fetchCountryCurrencyCode(Request $request): JsonResponse
    {
        $content = $request->getContent();
        $data = json_decode($content, true);
        $country = $data['country'] ?? null;

        if (empty($country)) {
            return new JsonResponse(['error' => 'Country parameter is required'], Response::HTTP_BAD_REQUEST);
        }

        $token = $this->getParameter('app.country_currency_api_token');
        $url = 'https://aaapis.com/api/v1/info/country/';

        try {
            $response = $this->httpClient->request('POST', $url, [
                'headers' => [
                    'Authorization' => "Token {$token}",
                    'Content-Type' => 'application/json',
                ],
                'json' => ['country' => $country],
            ]);

            $statusCode = $response->getStatusCode();

            if (200 !== $statusCode) {
                return new JsonResponse(['error' => 'Failed to fetch country currency'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $data = $response->toArray();

            if (!isset($data['currency']['code'])) {
                return new JsonResponse(['error' => 'Invalid response from external API'], Response::HTTP_BAD_REQUEST);
            }

            return new JsonResponse(['currency' => $data['currency']]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/convert-currency', name: 'api_convert_currency', methods: ['POST'])]
    public function convertCurrency(Request $request): JsonResponse
    {
        $content = $request->getContent();
        $data = json_decode($content, true);
        $fromCurrency = $data['fromCurrency'] ?? null;
        $toCurrency = $data['toCurrency'] ?? null;
        $amount = (float) ($data['amount'] ?? 0);

        if (empty($fromCurrency) || empty($toCurrency) || $amount <= 0) {
            return new JsonResponse(['error' => 'Invalid request data'], Response::HTTP_BAD_REQUEST);
        }

        $apiKey = $this->getParameter('app.exchange_api_key');
        $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$fromCurrency}";

        try {
            $response = $this->httpClient->request('GET', $url);
            $statusCode = $response->getStatusCode();

            if (200 !== $statusCode) {
                return new JsonResponse(['error' => 'Failed to fetch conversion rates'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $data = $response->toArray();
            $rates = $data['conversion_rates'] ?? [];

            if (!isset($rates[$toCurrency])) {
                return new JsonResponse(['error' => 'Currency code not found'], Response::HTTP_BAD_REQUEST);
            }

            $convertedAmount = $amount * $rates[$toCurrency];

            return new JsonResponse(['convertedAmount' => $convertedAmount]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/flights/filter', name: 'app_flights_filter', methods: ['GET'])]
    public function filterFlightsByDateRange(Request $request, OffresVoyageRepository $voyageService): Response
    {
        $departureDate = $request->query->get('departureDate');
        $returnDate = $request->query->get('returnDate');

        if (!$departureDate || !$returnDate) {
            $this->addFlash('error', 'Please provide both departure and return dates.');

            return $this->redirectToRoute('app_flights');
        }

        $voyages = $voyageService->findByDateRange(new \DateTime($departureDate), new \DateTime($returnDate));

        return $this->render('flights/index.html.twig', [
            'voyages' => $voyages,
        ]);
    }

    #[Route('/flights/calendar', name: 'app_flights_calendar')]
    public function calendar(): Response
    {
        return $this->render('flights/calendar.html.twig');
    }
}
