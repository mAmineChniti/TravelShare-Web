<?php

// src/Controller/ExcursionsController.php

namespace App\Controller;

use App\Entity\Excursions;
use App\Form\ExcursionsType;
use Psr\Log\LoggerInterface;
use App\Service\NotificationService;
use App\Repository\ExcursionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExcursionsController extends AbstractController
{
    #[Route('/excursions', name: 'app_excursions')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_excursions_read');
    }

    #[Route('/excursions/read', name: 'app_excursions_read')]
    public function read(ExcursionsRepository $excursionsRepository, NotificationService $notificationService): Response
    {
        // Récupère les excursions passées
        $pastExcursions = $notificationService->checkPastExcursions();

        // Crée les notifications formatées
        $notifications = [];
        foreach ($pastExcursions as $excursion) {
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'exclamation-triangle',
                'message' => sprintf('Excursion "%s" est terminée depuis le %s',
                    $excursion->getTitle(),
                    $excursion->getDateExcursion()->format('d/m/Y')
                ),
                'createdAt' => $excursion->getDateExcursion(),
            ];
        }

        return $this->render('excursions/readExcursion.html.twig', [
            'excursions' => $excursionsRepository->findAllWithGuides(),
            'notifications' => $notifications, // On passe les notifications au template
        ]);
    }

    #[Route('/excursions/new', name: 'app_excursions_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $excursion = new Excursions();
        $form = $this->createForm(ExcursionsType::class, $excursion);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($excursion);
                $entityManager->flush();

                $this->addFlash('success', 'Excursion créée avec succès');

                return $this->redirectToRoute('app_excursions_read');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création: '.$e->getMessage());
            }
        }

        return $this->render('excursions/addExcursion.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/excursions/edit/{id}', name: 'app_excursions_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Excursions $excursion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExcursionsType::class, $excursion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'excursion a été modifiée avec succès.');

            return $this->redirectToRoute('app_excursions_read');
        }

        return $this->render('excursions/editExcursion.html.twig', [
            'excursion' => $excursion,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/excursions/delete/{id}', name: 'app_excursions_delete', methods: ['POST'])]
    public function delete(Request $request, Excursions $excursion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$excursion->getExcursionId(), $request->request->get('_token'))) {
            $entityManager->remove($excursion);
            $entityManager->flush();

            $this->addFlash('success', 'L\'excursion a été supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide, suppression annulée.');
        }

        return $this->redirectToRoute('app_excursions_read');
    }

    #[Route('/generate-description', name: 'app_excursions_generate_description', methods: ['POST'])]
    public function generateDescription(Request $request, YourAIService $aiService, LoggerInterface $logger): Response
    {
        $title = $request->request->get('title');
        if (empty($title)) {
            $logger->error('Titre manquant pour la génération');

            return $this->json(['error' => 'Le titre est requis'], 400);
        }

        try {
            $description = $aiService->generateDescription($title);

            return $this->json(['description' => $description]);
        } catch (\Exception $e) {
            $logger->critical('Échec OpenAI : '.$e->getMessage());

            return $this->json(['error' => 'Erreur : '.$e->getMessage()], 500);
        }
    }
}
