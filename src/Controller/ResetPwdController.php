<?php

namespace App\Controller;

use App\Entity\enduser;
use App\Form\ResetPwdType;
use App\Repository\enduserRepository;
use App\Security\EmailVerifier;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class ResetPwdController extends AbstractController
{

    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/resetpwd', name: 'app_reset_pwd')]
    public function resetPwd(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        // Create the form with the user entity
        $form = $this->createForm(ResetPwdType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'OTP saisi par l'utilisateur depuis la requête
            #$userOTP = $request->request->get('form')['otp'];
            $emailSaisie = $form->get('email')->getData();

            $userRepository = $doctrine->getRepository(enduser::class);
            $user = $userRepository->findOneBy(['email_user' => $emailSaisie]);

            //redirect the user to the app_main route
            if($user->getIsBanned() == 0){
                return $this->redirectToRoute('send_email', ['emailSaisie' => $emailSaisie]);
            }else{
                $this->addFlash('error', 'Votre compte a été banni.');
                return $this->redirectToRoute('app_login');
            }
        }


        return $this->render('reset_pwd/reset_pwd.html.twig', [
            'controller_name' => 'ResetPwdController',
            'form' => $form->createView(),
        ]);
    }
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
