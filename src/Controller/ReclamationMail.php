<?php

// src/Controller/MailController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport; // Corrected namespace
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ReclamationMail extends AbstractController
{
    #[Route('/reclamation/{emailSaisie}/{subject}/{message}', name: 'send_mail_reclamation')]
    public function sendMail(MailerInterface $mailer, UrlGeneratorInterface $urlGenerator,$emailSaisie,$subject,$message): Response
    {
        // Load MAILER_DSN from environment variables
        $mailerDsn = $_ENV['MAILER_DSN'] ?? null;

        // Generate OTP
        $emailsaisie = $emailSaisie;
        $subject = $subject;
        $message = $message;

        $email = (new Email())
            ->from('wertatanifadi@gmail.com')
            ->to($emailsaisie)
            ->subject($subject)
            ->text($message);

        try {
            // Check if MAILER_DSN is set
            if (!$mailerDsn) {
                throw new \InvalidArgumentException("MAILER_DSN is not configured.");
            }

            // Create a new mailer instance with the provided DSN
            $transport = Transport::fromDsn($mailerDsn);
            $customMailer = new Mailer($transport);

            // Send the email
            $customMailer->send($email);
            $responseMessage = 'Email sent successfully!';
        } catch (TransportExceptionInterface $e) {
            $responseMessage = 'Failed to send email: ' . $e->getMessage();
        } catch (\InvalidArgumentException $e) {
            $responseMessage = $e->getMessage();
        }
        

        return $this->redirectToRoute('afficherReclamationFA');
    }

    
}
