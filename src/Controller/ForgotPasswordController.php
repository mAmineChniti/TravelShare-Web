<?php

namespace App\Controller;

use App\Entity\Users;
use Psr\Log\LoggerInterface;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ForgotPasswordController extends AbstractController
{
    private MailerService $mailerService;
    private LoggerInterface $logger;
    private SessionInterface $session;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        MailerService $mailerService,
        LoggerInterface $logger,
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ) {
        $this->mailerService = $mailerService;
        $this->logger = $logger;
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/forgot/password', name: 'app_forgot_password')]
    public function index(Request $request): Response
    {
        $error = null;
        $success = null;
        $showCodeFields = false;
        $showPasswordField = false;
        $email = $request->request->get('email');

        // Handle Send Code request
        if ($request->isMethod('POST')) {
            if ($request->request->has('send_code')) {
                if (empty($email)) {
                    $error = 'Email address is required.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Please enter a valid email address.';
                } else {
                    $user = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);

                    if (!$user) {
                        $error = 'No account found with this email address.';
                    } else {
                        $resetCode = (string) random_int(100000, 999999);
                        $this->session->set('reset_code', $resetCode);
                        $this->session->set('reset_email', $email);
                        $this->session->set('reset_code_expires_at', time() + 300);

                        if ($this->mailerService->sendResetPasswordEmail($email, $resetCode)) {
                            $success = 'A verification code has been sent to your email address.';
                            $showCodeFields = true;
                        } else {
                            $error = 'Failed to send verification email. Please try again later.';
                        }
                    }
                }
            }
            // Handle Verify Code request
            elseif ($request->request->has('verify_code')) {
                $submittedCode = implode('', (array) $request->request->all('code'));
                $storedCode = $this->session->get('reset_code');
                $expiresAt = $this->session->get('reset_code_expires_at');

                if (time() > $expiresAt) {
                    $error = 'The code has expired. Please request a new one.';
                    $this->session->remove('reset_code');
                    $this->session->remove('reset_code_expires_at');
                } elseif (empty($submittedCode) || 6 !== strlen($submittedCode)) {
                    $error = 'Please enter the complete 6-digit code.';
                    $showCodeFields = true;
                } elseif ($submittedCode !== $storedCode) {
                    $error = 'Invalid verification code. Please try again.';
                    $showCodeFields = true;
                } else {
                    $showPasswordField = true;
                    // Ne pas afficher à nouveau les champs de code
                    $showCodeFields = false;
                }
            }
            // Handle Password Reset request
            elseif ($request->request->has('reset_password')) {
                $newPassword = $request->request->get('new_password');
                $email = $this->session->get('reset_email');

                if (empty($newPassword)) {
                    $error = 'New password is required.';
                    $showPasswordField = true;
                } else {
                    $user = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);

                    if ($user) {
                        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
                        $user->setPassword($hashedPassword);
                        $this->entityManager->flush();

                        $this->session->clear();
                        $this->addFlash('success', 'Your password has been reset successfully.');

                        return $this->redirectToRoute('app_login');
                    }
                }
            }
        }

        return $this->render('forgot_password/index.html.twig', [
            'error' => $error,
            'success' => $success,
            'show_code_fields' => $showCodeFields,
            'show_password_field' => $showPasswordField,
            'email' => $email ?? null,
        ]);
    }
}
