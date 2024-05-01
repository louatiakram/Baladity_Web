<?php

namespace App\Controller;

use App\Entity\enduser;
use App\Entity\muni;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileFronController extends AbstractController
{
    #[Route('/front/profile', name: 'app_profile_fron')]
    public function profileFront(Request $request, ManagerRegistry $doctrine): Response
    {

        $userId = $request->getSession()->get('user_id');


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
    public function profileEdit(Request $request, ManagerRegistry $doctrine): Response
    {

        $userId = $request->getSession()->get('user_id');


        //get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $user = $userRepository->findOneBy(['id_user' => $userId]);

        //get muni name
        $muniId = $user->getIdMuni();
        $muniRepository = $doctrine->getRepository(muni::class);
        $muni = $muniRepository->findOneBy(['id_muni' => $muniId]);
        $muniName = $muni->getNomMuni();



        return $this->render('profile_fron/profile_edit.html.twig', [
            'controller_name' => 'ProfileFronController',
            'user' => $user,
        ]);
    }


}
