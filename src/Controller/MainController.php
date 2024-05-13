<?php

namespace App\Controller;

use App\Entity\enduser;
use App\Repository\enduserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function indexfront(Request $request, ManagerRegistry $doctrine, enduserRepository $userRepository): Response
    {
        $userId = $request->getSession()->get('user_id');
        //$userId=81;

        // Count users per type
        $userCounts = $userRepository->countUsersPerType();

        //get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $users = $userRepository->findOneBy(['id_user' => $userId]);

        return $this->render('main/index_front.html.twig',[
            'user' => $users,
            'userCounts' => $userCounts,
        ]);
    }
    #[Route('/about', name: 'about')]
    public function about(Request $request, ManagerRegistry $doctrine): Response
    {
        $userId = $request->getSession()->get('user_id');
        //$userId=81;

        //get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $users = $userRepository->findOneBy(['id_user' => $userId]);

        return $this->render('about.html.twig',[
            'user' => $users,  
        ]);
    }

    #[Route('/hello', name: 'hello_world')]
    public function hello(): Response
    {
        return $this->render('hello_world/hello.html.twig');
    }
    
}