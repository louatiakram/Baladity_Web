<?php

namespace App\Controller;

use App\Repository\enduserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/profile', name: 'profile_user')]
    public function profile(): Response
    {
        return $this->render('user/profile.html.twig', [
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
