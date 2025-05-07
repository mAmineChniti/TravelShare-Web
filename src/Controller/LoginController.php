<?php

namespace App\Controller;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // If user is already logged in, redirect to home
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Get login error and last username entered
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // Create the login form with the last username
        $form = $this->createForm(LoginFormType::class, [
            '_email' => $lastUsername, // Pre-fill email field with last username
        ]);

        // Note: We don't need to handle form submission here because:
        // - Security bundle handles authentication via check_path (app_login)
        // - The form submission is intercepted by Symfony's security system

        return $this->render('login/index.html.twig', [
            'loginForm' => $form->createView(),
            'last_username' => $lastUsername, // Still pass this for manual form rendering
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // This method can be blank - it will be intercepted by the logout key on your firewall
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
