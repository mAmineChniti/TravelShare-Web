<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Notification;
use App\Entity\Reclamations;
use App\Service\ProfanityFilter;
use App\Form\ReclamationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function ajouter(Request $request, EntityManagerInterface $em, ProfanityFilter $profanityFilter): Response
    {
        // $this->d/enyAccessUnlessGranted('ROLE_USER');

        $reclamation = new Reclamations();

        // Set the user before handling the form
        $reclamation->setUser($this->getUser());

        $form = $this->createForm(ReclamationFormType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $title = $reclamation->getTitle();
            $description = $reclamation->getDescription();

            // Profanity check
            if ($profanityFilter->containsProfanity($title) || $profanityFilter->containsProfanity($description)) {
                $badWords = array_intersect(
                    array_merge(
                        $profanityFilter->findProfanities($title),
                        $profanityFilter->findProfanities($description)
                    ),
                    $profanityFilter->getProfanityWords()
                );

                $this->addFlash('warning', sprintf(
                    'Your message contains inappropriate terms (%s). Please rephrase your content.',
                    implode(', ', array_unique($badWords))
                ));

                return $this->redirectToRoute('app_reclamation');
            }

            // Save the complaint if no profanity found
            $reclamation->setDateReclamation(new \DateTime());

            $em->persist($reclamation);
            $em->flush();

            // Get admin user (role = 1)
            $admin = $em->getRepository(Users::class)->findOneBy(['role' => 1]);

            /** @var Users $user */
            $user = $this->getUser();

            if (!$user instanceof Users) {
                throw new \Exception('The user must be an instance of App\\Entity\\Users.');
            }

            // Create notification for admin
            $notification = new Notification();
            $notification->setMessage('New complaint submitted by '.$user->getName());
            $notification->setIsRead(false);
            $notification->setCreatedAt(new \DateTime());
            $notification->setUpdatedAt(new \DateTime());
            $notification->setUser($admin);

            $em->persist($notification);
            $em->flush();

            $this->addFlash('success', 'Your complaint has been successfully submitted!');

            return $this->redirectToRoute('app_list_rec_user');
        }

        // Get notifications for current user
        $notifications = $em->getRepository(Notification::class)->findBy([
            'user' => $this->getUser(),
        ], ['createdAt' => 'DESC']);

        return $this->render('reclamation/index.html.twig', [
            'form' => $form->createView(),
            'profanity_words' => $profanityFilter->getProfanityWords(),
            'notifications' => $notifications,
        ]);
    }
}
