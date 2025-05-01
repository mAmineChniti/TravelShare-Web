<?php

namespace App\Controller;

use App\Entity\Hotels;
use App\Repository\HotelsRepository;
use App\Repository\ChambresRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HotelsController extends AbstractController
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/hotels', name: 'app_hotels')]
    public function index(HotelsRepository $hotelsRepository, Request $request): Response
    {
        $search = $request->query->get('search', '');
        $filterDescriptions = $request->query->all('description');

        if (!empty($filterDescriptions)) {
            $qb = $hotelsRepository->createQueryBuilder('h');

            foreach ($filterDescriptions as $key => $description) {
                $qb->andWhere('h.description LIKE :description' . $key)
                    ->setParameter('description' . $key, '%' . $description . '%');
            }
            if ($search) {
                $qb->andWhere('h.nom LIKE :search OR h.adress LIKE :search')
                    ->setParameter('search', '%' . $search . '%');
            }

            $hotels = $qb->getQuery()->getResult();
        } else {
            $hotels = $hotelsRepository->findAll();
        }

        foreach ($hotels as $hotel) {
            if ($hotel->getImageH()) {
                $hotel->base64Image = base64_encode(stream_get_contents($hotel->getImageH()));
            } else {
                $hotel->base64Image = null;
            }
        }

        // Prepare a detailed prompt with hotel names and descriptions for the AI
        $hotelSummaries = [];
        foreach ($hotels as $hotel) {
            $hotelSummaries[] = 'Hotel: ' . $hotel->getNom() . "\nDescription: " . $hotel->getDescription();
        }

        $prompt = "Select the top 3 hotels from the following list and provide a concise, compelling reason for each choice do not write anything else. Format strictly as:
1. [Hotel Name]: [Reason]
2. [Hotel Name]: [Reason]
3. [Hotel Name]: [Reason]

Hotel List:\n" . implode("\n", $hotelSummaries);

        $geminiApiKey = $this->getParameter('gemini_api_key');
        if (!$geminiApiKey) {
            throw new \RuntimeException('Gemini API key not configured.');
        }
        // Call the Gemini API with the updated endpoint and payload structure
        $response = $this->httpClient->request('POST', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $geminiApiKey, [
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ],
        ]);

        $data = $response->toArray(false); // Set false to avoid exception on non-2xx responses

        // Log the API response for debugging
        /**$logFilePath = __DIR__ . '/../../var/log/ai_recommendation.log';
        file_put_contents($logFilePath, print_r($data, true));**/

        $recommendation = null;
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $recommendation = $data['candidates'][0]['content']['parts'][0]['text'];
        }

        return $this->render('hotels/index.html.twig', [
            'hotels' => $hotels,
            'filterDescriptions' => $filterDescriptions,
            'descriptionOptions' => [
                'Always sunny',
                'Partly cloudy',
                'Rainy retreat',
                'Beachfront',
                'Forest nearby',
                'Mountain view',
                'Spa & wellness',
                'City center',
                'Budget-friendly',
                'Family-friendly',
            ],
            'recommendation' => $recommendation,
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

            /** @var string[] $selected */
            $selected = $request->request->all()['description'] ?? [];

            if (count($selected) > 0) {
                // implode into a comma-separated string for your TEXT column
                $hotel->setDescription(implode(',', $selected));
            } else {
                $errorMessages[] = 'At least one feature must be selected.';
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

            /** @var string[] $selected */
            $selected = $request->request->all()['description'] ?? [];

            if (count($selected) > 0) {
                $hotel->setDescription(implode(',', $selected));
            } else {
                $errorMessages[] = 'At least one feature must be selected.';
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
    public function viewRooms(
        int $hotelId,
        HotelsRepository $hotelsRepository,
        ChambresRepository $chambresRepository,
    ): Response {
        $hotel = $hotelsRepository->find($hotelId);
        if (!$hotel) {
            throw $this->createNotFoundException('Hotel not found');
        }

        $rooms = $chambresRepository->findBy(['hotel' => $hotelId]);

        // ─── Unsplash API call ───
        $unsplashImages = [];
        $unsplashClientId = $this->getParameter('app.unsplash_client_id');
        if (!$unsplashClientId) {
            throw new \RuntimeException('Unsplash client ID not configured.');
        }
        try {
            $response = $this->httpClient->request('GET', 'https://api.unsplash.com/search/photos', [
                'query' => [
                    'query' => $hotel->getNom(),
                    'per_page' => 4,
                ],
                'headers' => [
                    'Authorization' => 'Client-ID ' . $unsplashClientId,
                ],
            ]);
            $data = $response->toArray(false);
            foreach ($data['results'] ?? [] as $item) {
                $unsplashImages[] = $item['urls']['regular'];
            }
        } catch (\Exception) {
            // log $e if you want, but leave $unsplashImages as empty array
        }

        $response = $this->render('hotels/rooms.html.twig', [
            'hotel' => $hotel,
            'rooms' => $rooms,
            'unsplashImages' => $unsplashImages,
        ]);

        // Set cache headers to make photos stay longer (1 day)
        $response->setSharedMaxAge(86400); // 86400 seconds = 1 day
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}
