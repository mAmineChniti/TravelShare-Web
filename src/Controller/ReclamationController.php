<?php

namespace App\Controller;

use App\Entity\Reclamations;
use App\Form\ReclamationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function ajouter(Request $request, EntityManagerInterface $em): Response
    {
        $reclamation = new Reclamations();
        $form = $this->createForm(ReclamationFormType::class, $reclamation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrement dans la base
            $reclamation->setUser($this->getUser()); // Si relation avec l'utilisateur
            $em->persist($reclamation);
            $em->flush();

            // Redirection après soumission réussie
            return $this->redirectToRoute('app_list_rec_user');
        }

        return $this->render('reclamation/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
