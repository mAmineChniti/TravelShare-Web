<?php

namespace App\Controller;
use App\Entity\Excursions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


class BookExcursionsController extends AbstractController
{
    public function __construct(
        #[Autowire('%stripe_secret_key%')]
        private string $stripeSecretKey,
        private LoggerInterface $logger
    ) {
    }

    #[Route('/book/excursions/{id}', name: 'app_book_excursions', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): Response
    {
        $excursion = $em->getRepository(Excursions::class)->find($id);
        
        if (!$excursion) {
            throw $this->createNotFoundException('Excursion not found');
        }

        return $this->render('book_excursions/index.html.twig', [
            'excursion' => $excursion,
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
    }

    #[Route('/book/excursions/{id}/pay', name: 'app_process_payment', methods: ['POST'])]
    public function processPayment(Request $request, int $id, EntityManagerInterface $em): JsonResponse
    {
        // Vérification du content type
        if (0 !== strpos($request->headers->get('Content-Type'), 'application/json')) {
            return $this->json([
                'error' => 'Invalid content type',
                'message' => 'Seul le JSON est accepté'
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        
        // Validation du JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json([
                'error' => 'Invalid JSON',
                'message' => json_last_error_msg()
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validation des champs requis
        $requiredFields = ['paymentMethodId', 'customerName', 'customerEmail'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->json([
                    'error' => 'Missing field',
                    'message' => "Le champ $field est requis"
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        try {
            $excursion = $em->getRepository(Excursions::class)->find($id);
            if (!$excursion) {
                return $this->json([
                    'error' => 'Excursion not found',
                    'message' => 'Excursion introuvable'
                ], Response::HTTP_NOT_FOUND);
            }

            Stripe::setApiKey($this->stripeSecretKey);

            // Création du PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => $excursion->getPrix() * 100,
                'currency' => 'eur',
                'payment_method' => $data['paymentMethodId'],
                'confirm' => true,
                'return_url' => $this->generateUrl(
                    'app_payment_success', 
                    ['id' => $id], 
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'receipt_email' => $data['customerEmail'],
                'metadata' => [
                    'excursion_id' => $id,
                    'customer_name' => $data['customerName'],
                    'customer_email' => $data['customerEmail']
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ]
            ]);

            return $this->json([
                'status' => $paymentIntent->status,
                'clientSecret' => $paymentIntent->client_secret,
                'requiresAction' => $paymentIntent->status === 'requires_action',
                'paymentIntentId' => $paymentIntent->id
            ]);

        } catch (ApiErrorException $e) {
            $this->logger->error('Stripe API Error: '.$e->getMessage());
            return $this->json([
                'error' => 'Stripe Error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            $this->logger->error('Payment Error: '.$e->getMessage());
            return $this->json([
                'error' => 'Payment Error',
                'message' => 'Une erreur est survenue lors du traitement du paiement'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/payment/success/{id}', name: 'app_payment_success')]
    public function paymentSuccess(int $id, EntityManagerInterface $em): Response
    {
        $excursion = $em->getRepository(Excursions::class)->find($id);
        
        if (!$excursion) {
            throw $this->createNotFoundException('Excursion not found');
        }
        
        return $this->render('book_excursions/success.html.twig', [
            'excursion' => $excursion,
        ]);
    }
}