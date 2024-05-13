<?php

namespace App\Controller;

use App\Entity\enduser;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $user = new enduser();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $emailSaisie = $form->get('email_user')->getData();
            $userRepository = $doctrine->getRepository(enduser::class);
            $isUserExist = $userRepository->findOneBy(['email_user' => $emailSaisie]);


            if ($isUserExist) {
                // Add a form error for the email field
                $form->get('email_user')->addError(new FormError('User with this email already exists.'));
                // Render the form again with the error message
                return $this->render('register.html.twig', [
                    'f' => $form->createView()
                ]);
            }



            //// encode the plain password
            //$user->setPassword(
            //$userPasswordHasher->hashPassword(
            //        $user,
            //        $form->get('plainPassword')->getData()
            //    )
            //);
            $user->setPassword($form->get('plainPassword')->getData());

            $image = $form->get('image_user')->getData();
            if ($image) {
                // Handle image upload and persist its filename to the database
                $fileName = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move($this->getParameter('uploads_directory'), $fileName);
                    // Set the image filename to the user entity
                    $user->setImageUser($fileName);
                } catch (FileException $e) {
                    // Handle the exception if file upload fails
                    // For example, log the error or display a flash message
                }
            }

            $user->setTypeUser('Citoyen');
            $user->setIsBanned(0);

            $entityManager->persist($user);
            $entityManager->flush();
            // Storing user ID in the session
            $request->getSession()->set('user_id', $user->getIdUser());



            #return $this->redirectToRoute('send_mail');
            return $this->redirectToRoute('send_mail', ['emailSaisie' => $emailSaisie]);

        }

        return $this->render('register.html.twig', [
            'f' => $form->createView(),
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
