<?php

namespace App\Controller;

use App\Entity\Reclamations;
use App\Repository\ReclamationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

final class ListRecUserController extends AbstractController
{
    #[Route('/list/rec/user', name: 'app_list_rec_user')]
    public function listAll(ReclamationsRepository $reclamationsRepository): Response
    {
        $user = $this->getUser();

        $reclamations = $user ? $reclamationsRepository->findBy(['user' => $user]) : [];

        return $this->render('list_rec_user/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/reclamation/edit/{id}', name: 'app_reclamation_edit', methods: ['POST'])]
    public function editReclamation(
        int $id,
        Request $request,
        ReclamationsRepository $reclamationsRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $reclamation = $reclamationsRepository->find($id);
        $user = $this->getUser();

        if (!$reclamation || $reclamation->getUser() !== $user) {
            $this->addFlash('error', 'Réclamation introuvable ou accès refusé.');
            return $this->redirectToRoute('app_list_rec_user');
        }

        // Optionnel : vérifie le CSRF token
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('edit_reclamation', $token)) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_list_rec_user');
        }

        $reclamation->setTitle($request->request->get('title'));
        $reclamation->setDescription($request->request->get('description'));
        $entityManager->flush();

        $this->addFlash('success', 'Réclamation mise à jour avec succès.');
        return $this->redirectToRoute('app_list_rec_user');
    }



    #[Route('/reclamation/delete/{id}', name: 'app_delete_rec', methods: ['POST'])]
    public function deleteReclamation(
        Request $request,
        int $id,
        ReclamationsRepository $reclamationsRepository,
        EntityManagerInterface $entityManager,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        $user = $this->getUser();
        $reclamation = $reclamationsRepository->find($id);

        if (!$reclamation || $reclamation->getUser() !== $user) {
            $this->addFlash('error', 'Réclamation introuvable ou accès refusé.');
            return $this->redirectToRoute('app_list_rec_user');
        }

        $submittedToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete' . $reclamation->getReclamationId(), $submittedToken))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('app_list_rec_user');
        }

        $entityManager->remove($reclamation);
        $entityManager->flush();

        $this->addFlash('success', 'Réclamation supprimée avec succès.');
        return $this->redirectToRoute('app_list_rec_user');
    }

}
