<?php

namespace App\Controller;

use App\Form\OtpType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OtpPwdController extends AbstractController
{
    #[Route('/otpPwd/{otp1}', name: 'app_otp_pwd')]
    public function otpPwd(Request $request,$otp1): Response
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
               return $this->redirectToRoute('app_change_pwd');
           }

           // If OTP is invalid, render the form again with an error message
           return $this->render('otp_verification/otp.html.twig', [
               'error' => 'Invalid OTP. Please try again.',
               'otp1' => $otp1,
           ]);
       }

        return $this->render('otp_pwd/otp_pwd.html.twig', [
            'controller_name' => 'OtpPwdController',
            'error' => 'Invalid OTP. Please try again.',
            'otp1' => $otp1,
            'form' => $form->createView(),
        ]);
    }
}
