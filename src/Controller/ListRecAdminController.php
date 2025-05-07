<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReclamationsRepository;
use App\Entity\Reponses;


final class ListRecAdminController extends AbstractController
{
    #[Route('/list/rec/admin', name: 'app_list_rec_admin')]
    public function listAll(ReclamationsRepository $reclamationsRepository, EntityManagerInterface $em): Response
    {
        // Vérification que l'utilisateur est un admin
        $user = $this->getUser();
        if ($user->getRole() !== 1) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à accéder à cette page.');
        }

        // Récupérer toutes les réclamations
        $reclamations = $reclamationsRepository->findAll();

        // Récupérer les notifications de l'administrateur (utilisateur connecté)
        $notifications = $em->getRepository(Notification::class)->findBy(
            ['user' => $user], // Filtrage des notifications pour l'utilisateur admin
            ['createdAt' => 'DESC'] // Tri par date (les plus récentes en premier)
        );

        return $this->render('list_rec_admin/index.html.twig', [
            'reclamations' => $reclamations,
            'notifications' => $notifications, // Passer les notifications à la vue
            'current_menu' => 'admin_users',
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

        // Créer et persister la réponse
        $reponse = new Reponses();
        $reponse->setContenu($message);
        $reponse->setReclamation($reclamation);
        $reponse->setDateReponse(new \DateTime());

        $em->persist($reponse);

        // Mettre à jour l'état de la réclamation
        $reclamation->setEtat('repondu'); // Changer l'état en "répondu"

        // Persister la réclamation mise à jour
        $em->persist($reclamation);
        $em->flush();

        $this->addFlash('success', 'Réponse envoyée avec succès !');
        return $this->redirectToRoute('app_list_rec_admin');
    }

}


