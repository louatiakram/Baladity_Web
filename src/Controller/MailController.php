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

class MailController extends AbstractController
{
    #[Route('/mail/{emailSaisie}', name: 'send_mail')]
    public function sendMail(MailerInterface $mailer, UrlGeneratorInterface $urlGenerator,$emailSaisie): Response
    {
        // Load MAILER_DSN from environment variables
        $mailerDsn = $_ENV['MAILER_DSN'] ?? null;

        // Generate OTP
        $otp = $this->generateOTP();
        $emailsaisie = $emailSaisie;

        $email = (new Email())
            ->from('wertatanifadi@gmail.com')
            ->to($emailsaisie)
            ->subject('Test Email with OTP')
            ->text("Your OTP is: $otp");

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
        
        #return $this->redirectToRoute('verify_otp');
        return $this->redirectToRoute('verify_otp', ['otp1' => $otp]);
    }

    private function generateOTP($length = 6)
    {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= mt_rand(0, 9);
        }
        return $otp;
    }
}
