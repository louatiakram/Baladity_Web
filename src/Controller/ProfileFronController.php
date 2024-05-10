<?php

namespace App\Controller;

use App\Entity\enduser;
use App\Entity\muni;
use App\Form\EditProfileType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileFronController extends AbstractController
{
    #[Route('/front/profile', name: 'app_profile_fron')]
    public function profileFront(Request $request, ManagerRegistry $doctrine): Response
    {

        $userId = $request->getSession()->get('user_id');
        #$userId = 141;

        //get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $user = $userRepository->findOneBy(['id_user' => $userId]);

        //get muni name
        $muniId = $user->getIdMuni();
        $muniRepository = $doctrine->getRepository(muni::class);
        $muni = $muniRepository->findOneBy(['id_muni' => $muniId]);
        $muniName = $muni->getNomMuni();



        return $this->render('profile_fron/profile_front.html.twig', [
            'controller_name' => 'ProfileFronController',
            'user' => $user,
        ]);
    }


    #[Route('/front/edit', name: 'app_profile_edit')]
    public function profileEdit(Request $request,UserPasswordHasherInterface $userPasswordHasher, ManagerRegistry $doctrine): Response
    {

        $entityManager = $doctrine->getManager();

        // Retrieving user ID from the session
        $userId = $request->getSession()->get('user_id');
        #$userId = 149;

        //get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $user = $userRepository->findOneBy(['id_user' => $userId]);

        //get muni name
        $muniId = $user->getIdMuni();
        $muniRepository = $doctrine->getRepository(muni::class);
        $muni = $muniRepository->findOneBy(['id_muni' => $muniId]);
        $muniName = $muni->getNomMuni();


        //edit
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Create the form for modifying the user
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
            );

            // Set the image_a field
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
    
            // Persist the modified actualite object to the database
            $entityManager->flush();
    
            // Redirect to a success page or display a success message
            // For example:
            return $this->redirectToRoute('app_profile_edit');
        }


        return $this->render('profile_fron/profile_edit.html.twig', [
            'controller_name' => 'ProfileFronController',
            'user' => $user,
            'muni' => $muni,
            'form' => $form->createView(),
        ]);
    }


}
