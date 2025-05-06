<?php

namespace App\Controller;

use App\Entity\Reponses;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReclamationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ListRecAdminController extends AbstractController
{
    #[Route('/list/rec/admin', name: 'app_list_rec_admin')]
    public function listAll(ReclamationsRepository $reclamationsRepository): Response
    {
        $reclamations = $reclamationsRepository->findAll();

        return $this->render('list_rec_admin/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/reclamation/{id}/reponse', name: 'app_add_response', methods: ['POST'])]
    public function addResponse(int $id, Request $request, ReclamationsRepository $reclamationsRepository, EntityManagerInterface $em): Response
    {
        $reclamation = $reclamationsRepository->find($id);
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée.');
        }

        $message = $request->request->get('responseText');

        if (empty($message)) {
            $this->addFlash('error', 'Le message de réponse ne peut pas être vide.');

            return $this->redirectToRoute('app_list_rec_admin');
        }

        $reponse = new Reponses();
        $reponse->setContenu($message);
        $reponse->setReclamation($reclamation); // This will now work
        $reponse->setDateReponse(new \DateTime());

        $em->persist($reponse);
        $em->flush();

        $this->addFlash('success', 'Réponse envoyée avec succès !');

        return $this->redirectToRoute('app_list_rec_admin');
    }
}
