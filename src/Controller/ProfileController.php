<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Users) {
            return $this->redirectToRoute('app_login');
        }

        $imageData = null;
        $photoContent = $user->getPhoto();
        if ($photoContent) {
            $imageData = base64_encode(is_resource($photoContent) ? stream_get_contents($photoContent) : $photoContent);
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

        // Handle profile image display
        $imageData = null;
        $photo = $user->getPhoto();
        if ($photo && is_resource($photo)) {
            $imageData = base64_encode(stream_get_contents($photo));
        } elseif (is_string($photo)) {
            $imageData = base64_encode($photo);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $confirmPassword = $form->get('confirm_password')->getData();

            if ($plainPassword && $plainPassword !== $confirmPassword) {
                $this->addFlash('error', 'Passwords do not match.');

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
                } catch (\Exception $e) {
                    $this->addFlash('error', 'An error occurred while uploading the photo.');
                }
            }

            $entityManager->flush();

            // $this->addFlash('success', 'Profile updated successfully.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'imageData' => $imageData,
        ]);
    }

    #[Route('/profile/delete', name: 'app_delete_account', methods: ['POST'])]
    public function deleteAccount(
        Request $request,
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage,
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Users) {
            return $this->redirectToRoute('app_login');
        }

        // Vérification CSRF
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete-account', $submittedToken)) {
            $this->addFlash('error', 'Invalid CSRF token');

            return $this->redirectToRoute('app_profile');
        }

        try {
            // Déconnexion avant suppression
            $tokenStorage->setToken(null);
            $request->getSession()->invalidate();

            // Suppression de l'utilisateur
            $entityManager->remove($user);
            $entityManager->flush();

            // $this->addFlash('success', 'Your account has been deleted successfully');
            return $this->redirectToRoute('app_home');
        } catch (\Exception $e) {
            // $this->addFlash('error', 'An error occurred while deleting your account');
            return $this->redirectToRoute('app_profile');
        }
    }
}
