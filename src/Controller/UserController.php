<?php

namespace App\Controller;

use App\Entity\enduser;
use App\Entity\muni;
use App\Form\RegisterType;
use App\Repository\enduserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    
    #[Route('/afficher', name: 'afficher_user')]
    public function afficher(enduserRepository $Rep): Response
    {
        $users = $Rep->findAll();
        return $this->render('user/afficherUser.html.twig', [
            'controller_name' => 'AuthorController',
            'users' => $users
        ]);
    }

    #[Route('/afficher/detail/{i}', name: 'user_detail')]
    public function detail($i, enduserRepository $rep): Response
    {
        $user = $rep->find($i);
        if (!$user) {
            throw $this->createNotFoundException('Task not found');
        }

        return $this->render('user/detail.html.twig', ['user' => $user]);
    }

    #[Route('/profile', name: 'profile_user')]
    public function profile(Request $request, ManagerRegistry $doctrine): Response
    {
        // Retrieving user ID from the session
        $userId = $request->getSession()->get('user_id');

        //get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $user = $userRepository->findOneBy(['id_user' => $userId]);

        //get muni name
        $muniId = $user->getIdMuni();
        $muniRepository = $doctrine->getRepository(muni::class);
        $muni = $muniRepository->findOneBy(['id_muni' => $muniId]);
        $muniName = $muni->getNomMuni();

        return $this->render('user/profile.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
            'muniName' => $muniName,
        ]);
    }

    #[Route('/update', name: 'update_user')]
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
        return $this->render('user/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user, // Pass the user entity to the template if needed
        ]);
    }

    #[Route('/user/delete/{i}', name: 'user_delete')]
    public function delete($i, enduserRepository $rep, ManagerRegistry $doctrine): Response
    {
        $xs = $rep->find($i);
        $em = $doctrine->getManager();
        $em->remove($xs);
        $em->flush();
        return $this->redirectToRoute('afficher_user');
    }

}
