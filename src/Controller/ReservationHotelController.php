<?php

namespace App\Controller;

use Endroid\QrCode\QrCode;
use App\Entity\ReservationHotel;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use App\Repository\ChambresRepository;
use Endroid\QrCode\ErrorCorrectionLevel;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ReservationHotelRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationHotelController extends AbstractController
{
    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    #[Route('/room/reserve/{roomId}', name: 'room_reserve', methods: ['POST'])]
    public function reserveRoom(
        int $roomId,
        Request $request,
        ChambresRepository $chambresRepository,
        ReservationHotelRepository $reservationHotelRepository,
    ): Response {
        $room = $chambresRepository->find($roomId);
        if (!$room) {
            return new JsonResponse(['error' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }

        $today = new \DateTime('today');

        $startStr = $request->request->get('startReservation');
        $endStr = $request->request->get('endReservation');

        if (!$startStr || !$endStr) {
            return new JsonResponse(['error' => 'Both start and end dates are required.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $start = new \DateTime($startStr);
            $end = new \DateTime($endStr);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format.'], Response::HTTP_BAD_REQUEST);
        }

        if ($start < $today) {
            return new JsonResponse(['error' => 'Start date cannot be in the past.'], Response::HTTP_BAD_REQUEST);
        }

        if ($end <= $start) {
            return new JsonResponse(['error' => 'End date must be after the start date.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $duration = $end->diff($start)->days;
            $reservation = new ReservationHotel();
            $reservation->setClientId(1);
            $reservation->setChambreId($roomId);
            $reservation->setDateDebut($start);
            $reservation->setDateFin($end);
            $reservation->setStatusEnu('');
            $reservation->setPrixTotale($room->getPrixParNuit() * $duration);

            $reservationHotelRepository->add($reservation);

            $this->addFlash('success', 'Room reserved successfully!');

            $data = sprintf(
                "Reservation Confirmed!\nRoom: %s\nCheck-in: %s\nCheck-out: %s\nTotal: %s TND",
                $room->getNumeroChambre(),
                $start->format('Y-m-d'),
                $end->format('Y-m-d'),
                $reservation->getPrixTotale()
            );

            $qrCode = new QrCode(
                data: $data,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,
                margin: 10
            );

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $qrDir = $this->getParameter('kernel.project_dir').'/public/qr-codes/';
            if (!is_dir($qrDir)) {
                mkdir($qrDir, 0755, true);
            }
            $qrCodePath = sprintf('%s/%s.png', $qrDir, bin2hex(random_bytes(8)));
            $result->saveToFile($qrCodePath);
            $mailer_password = $this->getParameter('app.mailer_password');

            if (!$mailer_password) {
                throw new \Exception('Mailer password not set in parameters.');
            }

            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'voyagepidev@gmail.com';
                $mail->Password = $mailer_password;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('voyagepidev@gmail.com', 'TRAVELSHARE');
                $mail->addAddress('bouhrem4@gmail.com');
                $mail->isHTML(true);
                $mail->Subject = 'Room Reservation Confirmation';
                $mail->Body = '
                    <div style="font-family: Arial, sans-serif; line-height:1.6;">
                        <h2 style="color:#4CAF50">Reservation Confirmed!</h2>
                        <p>Dear Client,</p>
                        <p>Your reservation for room <strong>'.$room->getNumeroChambre().'</strong> is now confirmed.</p>
                        <ul>
                            <li><strong>Check-in:</strong> '.$start->format('Y-m-d').'</li>
                            <li><strong>Check-out:</strong> '.$end->format('Y-m-d').'</li>
                            <li><strong>Total Price:</strong> '.$reservation->getPrixTotale().' TND</li>
                        </ul>
                        <p>We look forward to hosting you!</p>
                        <p><img src="cid:qr_code" alt="QR Code"></p>
                        <p style="font-size:0.9em;color:#888">Questions? Contact us at voyagepidev@gmail.com.</p>
                    </div>
                ';
                $mail->addEmbeddedImage($qrCodePath, 'qr_code', 'reservation.png', 'base64', 'image/png');
                $mail->send();
                $this->addFlash('info', 'A confirmation email has been sent to your email address.');
            } catch (Exception $e) {
                $this->addFlash('error', 'Mailer Error: '.$e->getMessage());
            }

            return $this->redirectToRoute('room_details', ['roomId' => $roomId]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while reserving the room: '.$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/geocode', name: 'geocode')]
    public function geocode(Request $request): JsonResponse
    {
        $address = $request->query->get('address');

        if (!$address) {
            return new JsonResponse(['error' => 'Address is required'], 400);
        }

        try {
            $response = $this->client->request('GET', 'https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                ],
                'headers' => [
                    'User-Agent' => 'YourAppName/1.0',
                ],
            ]);

            $data = json_decode($response->getContent(), true);

            if (!empty($data)) {
                return new JsonResponse([
                    'lat' => (float) $data[0]['lat'],
                    'lon' => (float) $data[0]['lon'],
                ]);
            }

            return new JsonResponse(['error' => 'Location not found'], 404);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/nearby-places', name: 'nearby_places')]
    public function getNearbyPlaces(Request $request): JsonResponse
    {
        $latitude = $request->query->get('lat');
        $longitude = $request->query->get('lon');
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return new JsonResponse(['error' => 'Invalid latitude or longitude'], 400);
        }
        $latitude = (float) $latitude;
        $longitude = (float) $longitude;
        try {
            $response = $this->client->request('GET', 'https://overpass-api.de/api/interpreter', [
                'query' => [
                    'data' => '[out:json];node(around:1000,'.$latitude.','.$longitude.')["amenity"~"restaurant|cafe|bar"];out;',
                ],
            ]);

            $data = json_decode($response->getContent(), true);

            $places = array_map(function ($place) {
                return [
                    'name' => $place['tags']['name'] ?? 'Unknown',
                    'type' => $place['tags']['amenity'] ?? 'Unknown',
                    'lat' => $place['lat'],
                    'lon' => $place['lon'],
                ];
            }, $data['elements'] ?? []);

            return new JsonResponse($places);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
