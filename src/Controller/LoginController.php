<?php

namespace App\Controller;

use App\Entity\enduser;
use App\Form\LoginType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/login', name: 'app_login')]
    public function login(ManagerRegistry $doctrine, Request $request, AuthenticationUtils $authenticationUtils): Response
    {

        // Create an instance of the Enduser entity
        $user = new Enduser();

        // Get any authentication error message
        $error = $authenticationUtils->getLastAuthenticationError();

        // Create the login form
        $form = $this->createForm(LoginType::class, $user);
        $form->handleRequest($request);


        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve the submitted data from the form
            $emailSaisie = $form->get('email_user')->getData();
            $passwordSaisie = $form->get('password')->getData();

            //$hashedPassword = $this->passwordEncoder->encodePassword($user, $form->get('password')->getData());
            //$user->setPassword($hashedPassword);

            // Retrieve the user from the database based on the provided email
            $userRepository = $doctrine->getRepository(Enduser::class);
            $user = $userRepository->findOneBy(['email_user' => $emailSaisie]);

            // Check if a user with the provided email exists
            if ($user) {
                // Verify if the password from the form matches the hashed password stored in the database
                $userpwd = $user->getPassword();
                //if ($this->passwordEncoder->isPasswordValid($user, $passwordSaisie)) {
                if ($passwordSaisie == $userpwd) {
                    // Password is correct, store user ID in session
                    $request->getSession()->set('user_id', $user->getIdUser());

                    // Password is correct
                    if ($user->getTypeUser() == "Admin") {
                        //redirect the user to the app_main route
                        return $this->redirectToRoute('app_main');
                    } else {
                        //redirect the user to the app_main route
                        if($user->getIsBanned() == 0){
                            return $this->redirectToRoute('app_main_front');
                        }else{
                            $this->addFlash('error', 'Votre compte a été banni.');
                            return $this->redirectToRoute('app_login');
                        }
                    }
                } else {
                    // Add a form error for incorrect password
                    $form->addError(new FormError('Invalid email or password.'));
                }
            } else {
                // Add a form error for user not found
                $form->addError(new FormError('User not found.'));
            }
        }

        // Render the login form
        return $this->render('login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

    #[Route('/sign-out', name: 'sign_out')]
    public function signOut(SessionInterface $session, Request $request): RedirectResponse
    {
        // Invalidate the session (logout the user)
        $session->invalidate();

        // Optionally, you can add a flash message indicating successful sign-out
        $this->addFlash('success', 'You have been signed out successfully.');

        // Redirect to the homepage or any other desired page
        return $this->redirectToRoute('app_login');
    }
}
