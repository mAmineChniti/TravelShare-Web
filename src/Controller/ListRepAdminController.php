<?php

namespace App\Controller;

use App\Entity\Reponses;
use App\Entity\Notification;
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
    public function listAll(ReponsesRepository $reponsesRepository, EntityManagerInterface $em): Response
    {
        // Vérification que l'utilisateur est un admin
        $user = $this->getUser();
        if (1 !== $user->getRole()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à accéder à cette page.');
        }

        // Récupérer les réponses des réclamations avec les informations liées
        $reponses = $reponsesRepository->createQueryBuilder('r')
            ->join('r.reclamation', 'rec')
            ->join('rec.user', 'u')
            ->select('r.reponseId, r.contenu, r.dateReponse, rec.title AS reclamationTitle, u.name AS userName')
            ->getQuery()
            ->getResult();

        // Récupérer les notifications de l'administrateur (utilisateur connecté)
        $notifications = $em->getRepository(Notification::class)->findBy(
            ['user' => $user], // Filtrage des notifications pour l'utilisateur admin
            ['createdAt' => 'DESC'] // Tri par date (les plus récentes en premier)
        );

        // Passer les réponses et notifications à la vue
        return $this->render('list_rep_admin/index.html.twig', [
            'reponses' => $reponses,
            'notifications' => $notifications, // Passer les notifications à la vue
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
