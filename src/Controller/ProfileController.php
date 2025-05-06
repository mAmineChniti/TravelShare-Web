<?php

namespace App\Controller;

<<<<<<< HEAD
use App\Entity\Users;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Users) {
            return $this->redirectToRoute('app_login');
        }

        $imageData = null;
        if ($user->getPhoto()) {
            $imageData = base64_encode(stream_get_contents($user->getPhoto()));
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'imageData' => $imageData,
        ]);
    }

    #[Route('/profile/update', name: 'app_update_account', methods: ['GET', 'POST'])]
    public function updateAccount(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Users) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        // Générer l'image en base64 si elle existe
        $imageData = null;
        if ($user->getPhoto()) {
            $imageData = base64_encode(stream_get_contents($user->getPhoto()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $confirmPassword = $form->get('confirm_password')->getData();

            if ($plainPassword && $plainPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');

                return $this->redirectToRoute('app_update_account');
            }

            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $uploadedFile = $form->get('photo')->getData();
            if ($uploadedFile) {
                try {
                    $stream = fopen($uploadedFile->getPathname(), 'rb');
                    $user->setPhoto(stream_get_contents($stream));
                    fclose($stream);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de la photo.');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès !');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'imageData' => $imageData,  // Passer l'image encodée en base64
        ]);
    }

    #[Route('/delete-account', name: 'app_delete_account', methods: ['POST'])]
    public function deleteAccount(
        Request $request,
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage,
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Users) {
            return $this->redirectToRoute('app_login');
        }

        // Validation CSRF
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete-account', $submittedToken)) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_profile');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        // Déconnexion de l'utilisateur
        $tokenStorage->setToken(null);
        $request->getSession()->invalidate();

        // $this->addFlash('success', 'Votre compte a été supprimé avec succès.');
        return $this->redirectToRoute('app_logup');
    }
=======
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
>>>>>>> origin/master
}
