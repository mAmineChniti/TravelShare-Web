<?php

namespace App\Controller;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailerController extends AbstractController
{
    #[Route('/send-email', name: 'send_email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('benmwiem@gmail.com')
            ->to('wiem.benmsahel@etudiant-fst.utm.tn')
            ->subject('Test Symfony Mailer')
            ->text('Ceci est un test d\'envoi d\'e-mail avec Symfony Mailer et Mailjet.');

        $mailer->send($email);

        return new Response('E-mail envoyé avec succès.');
    }
}
