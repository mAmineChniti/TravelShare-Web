<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class LogupController extends AbstractController
{
    #[Route('/logup', name: 'app_logup')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Hash password
                $plainPassword = $form->get('plainPassword')->getData();
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);

                // Set default values
                $user->setRole(0); // Default role
                $user->setCompte(0); // Inactive account
                $user->setPhoto("https://cdn-icons-png.flaticon.com/512/9187/9187604.png"); // Default avatar

                // Persist to database
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
                return $this->redirectToRoute('app_login');
            }

            // Show form errors
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        return $this->render('logup/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
