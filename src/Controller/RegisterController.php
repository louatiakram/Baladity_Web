<?php

namespace App\Controller;

use App\Entity\enduser;
use App\Form\RegisterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{

    
    #[Route('/register', name: 'register_user')]
    public function registerUser(ManagerRegistry $doctrine, Request $request): Response
    {
        $user = new enduser();

         // Set the user type to "Citoyen" by default
         $user->setTypeUser('Citoyen');
         
        $form = $this->createForm(RegisterType::class, $user);
        $form->remove('type_user'); // Remove the 'type_user' field from the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $emailSaisie = $form->get('email_user')->getData();
            $userRepository = $doctrine->getRepository(enduser::class);
            $isUserExist = $userRepository->findOneBy(['email_user' => $emailSaisie]);


            if ($isUserExist) {
                // Add a form error for the email field
                $form->get('email_user')->addError(new FormError('User with this email already exists.'));
                // Render the form again with the error message
                return $this->render('register/register.html.twig', [
                    'form' => $form->createView()
                ]);
            }
 
            
            $image = $form->get('image_user')->getData();
            if ($image) {
                // Handle image upload and persist its filename to the database
                $fileName = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move($this->getParameter('uploadsDirectory'), $fileName);
                    // Set the image filename to the user entity
                    $user->setImageUser($fileName);
                } catch (FileException $e) {
                    // Handle the exception if file upload fails
                    // For example, log the error or display a flash message
                }
            }

                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                // Storing user ID in the session
                $request->getSession()->set('user_id', $user->getIdUser());

                return $this->redirectToRoute('app_front_main');

        }

        return $this->render('register/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
