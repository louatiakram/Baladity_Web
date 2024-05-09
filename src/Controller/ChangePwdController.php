<?php

namespace App\Controller;

use App\Entity\enduser;
use App\Form\EditPasswordType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ChangePwdController extends AbstractController
{
    #[Route('/changePwd', name: 'app_change_pwd')]
    public function changePwd(Request $request, UserPasswordHasherInterface $userPasswordHasher, ManagerRegistry $doctrine): Response
    {

        $entityManager = $doctrine->getManager();

        $userId = $request->getSession()->get('user_id');

        //get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $user = $userRepository->findOneBy(['id_user' => $userId]);

        // Create the form for modifying the user
        $form = $this->createForm(EditPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
            );
    
            // Persist the modified actualite object to the database
            $entityManager->flush();
    
            // Redirect to a success page or display a success message
            // For example:
           // Password is correct
           return $this->redirectToRoute('app_login');
        }


        return $this->render('change_pwd/change_pwd.html.twig', [
            'controller_name' => 'ChangePwdController',
            'form' => $form->createView(),
        ]);
    }
}
