<?php

namespace App\Controller;

use App\Form\OtpType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OTPVerificationController extends AbstractController
{
    #[Route('/otpverification/{otp1}', name: 'verify_otp')]
    public function verifyOTP(Request $request, $otp1): Response
    {

        // Create the form with the user entity
        $form = $this->createForm(OtpType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'OTP saisi par l'utilisateur depuis la requête
            #$userOTP = $request->request->get('form')['otp'];
            $userOTP = $form->get('otp')->getData();

            // Récupérer l'OTP envoyé par email (celui que vous avez stocké dans la session ou la base de données)
            $storedOTP = $otp1;

            // Vérifier si l'OTP saisi par l'utilisateur correspond à celui envoyé par email
            if ($userOTP === $storedOTP) {
                // OTP valide, vous pouvez effectuer les actions appropriées ici (par exemple, activer le compte utilisateur)
                // Rediriger vers une page de succès ou effectuer d'autres opérations nécessaires
                return $this->redirectToRoute('app_main_front');
            }

            // If OTP is invalid, render the form again with an error message
            return $this->render('otp_verification/otp.html.twig', [
                'error' => 'Invalid OTP. Please try again.',
                'otp1' => $otp1,
            ]);
        }

        // OTP invalide, afficher un message d'erreur ou rediriger vers une page d'échec
        return $this->render('otp_verification/otp.html.twig', [
            'error' => 'Invalid OTP. Please try again.',
            'otp1' => $otp1,
            'form' => $form->createView(),
        ]);
    }
}
