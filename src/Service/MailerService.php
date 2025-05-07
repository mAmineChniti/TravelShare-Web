<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailerService
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger
    ) {
    }

    public function sendResetPasswordEmail(string $to, string $code): bool
    {
        $email = (new Email())
            ->from('benmwiem@gmail.com')
            ->to($to)
            ->subject('Your Password Reset Code')
            ->html($this->getEmailTemplate($code));

        try {
            $this->mailer->send($email);
            $this->logger->info("Password reset email sent to {$to}");
            return true;
        } catch (TransportExceptionInterface $e) {
            $this->logger->error("Failed to send reset email: {$e->getMessage()}");
            return false;
        }
    }

    private function getEmailTemplate(string $code): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        .container { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .code { font-size: 24px; letter-spacing: 5px; color: #2563eb; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Password Reset Request</h2>
        <p>Here is your verification code:</p>
        <div class="code">{$code}</div>
        <p>This code will expire in 5 minutes.</p>
        <p>If you didn't request this, please ignore this email.</p>
    </div>
</body>
</html>
HTML;
    }
}