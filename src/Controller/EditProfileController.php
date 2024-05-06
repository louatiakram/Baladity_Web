<?php

namespace App\Controller;

use App\Entity\enduser;
use App\Form\RegisterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditProfileController extends AbstractController
{

    #[Route('/editUser', name: 'update_user')]
    public function updateUser(ManagerRegistry $doctrine, Request $request): Response
    {

        // Retrieving user ID from the session
        $userId = $request->getSession()->get('user_id');

        //get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $user = $userRepository->findOneBy(['id_user' => $userId]);

        // If the user doesn't exist, handle the case accordingly (e.g., show an error message)
        if (!$user) {
            // Handle the case where the user doesn't exist (e.g., show an error message)
            // You can redirect to an error page or any other action
            // For now, let's redirect to the main page
            return $this->redirectToRoute('app_main');
        }

        // Create the form with the user entity
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        // Handle form submission
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the updated user entity to the database
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            // Redirect to the main page or any other desired page after successful update
            return $this->redirectToRoute('app_main');
        }

        // Render the update form
        return $this->render('user\edit_profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user, // Pass the user entity to the template if needed
        ]);
    }
}
