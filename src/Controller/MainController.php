<?php

namespace App\Controller;

use App\Entity\enduser;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $userId = $request->getSession()->get('user_id');
        //$userId=81;

        //get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $users = $userRepository->findOneBy(['id_user' => $userId]);

        return $this->render('main/index.html.twig',[
            'user' => $users,    
        ]);
    }
    #[Route('/front', name: 'app_main_front')]
    public function indexfront(Request $request, ManagerRegistry $doctrine): Response
    {
        $userId = $request->getSession()->get('user_id');
        //$userId=81;

        //get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $users = $userRepository->findOneBy(['id_user' => $userId]);

        return $this->render('main/index_front.html.twig',[
            'user' => $users,  
        ]);
    }
    
}
