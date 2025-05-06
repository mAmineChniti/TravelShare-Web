<?php

namespace App\Controller;

use App\Entity\Reponses;
use App\Repository\ReponsesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ListRepAdminController extends AbstractController
{
    #[Route('/list/rep/admin', name: 'app_list_rep_admin')]
    public function listAll(ReponsesRepository $reponsesRepository): Response
    {
        $reponses = $reponsesRepository->createQueryBuilder('r')
            ->join('r.reclamation', 'rec')
            ->join('rec.user', 'u')
            ->select('r.reponseId, r.contenu, r.dateReponse, rec.title AS reclamationTitle, u.name AS userName')
            ->getQuery()
            ->getResult();

        return $this->render('list_rep_admin/index.html.twig', [
            'reponses' => $reponses,
        ]);
    }

    #[Route('/reponse/delete/{id}', name: 'reponse_delete', methods: ['GET'])]
    public function delete(Reponses $reponse, EntityManagerInterface $em): RedirectResponse
    {
        $em->remove($reponse);
        $em->flush();

        $this->addFlash('success', 'Réponse supprimée avec succès.');

        return $this->redirectToRoute('app_list_rep_admin');
    }

    #[Route('/reponse/edit/{id}', name: 'reponse_edit', methods: ['POST'])]
    public function edit(Reponses $reponse, Request $request, EntityManagerInterface $em): Response
    {
        $contenu = $request->request->get('contenu');

        if (null !== $contenu) {
            $reponse->setContenu($contenu);
            $em->flush();

            $this->addFlash('success', 'Réponse mise à jour avec succès.');
        } else {
            $this->addFlash('danger', 'Le contenu ne peut pas être vide.');
        }

        return $this->redirectToRoute('app_list_rep_admin');
    }
}
