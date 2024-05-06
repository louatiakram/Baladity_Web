<?php

namespace App\Controller;

use App\Entity\enduser;
use App\Entity\muni;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OverviewController extends AbstractController
{
    #[Route('/overview', name: 'app_overview')]
    public function overview(Request $request, ManagerRegistry $doctrine): Response
    {

        //$userId = $request->getSession()->get('user_id');
        $userId = 81;

        //get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $user = $userRepository->findOneBy(['id_user' => $userId]);

        //get muni name
        $muniId = $user->getIdMuni();
        $muniRepository = $doctrine->getRepository(muni::class);
        $muni = $muniRepository->findOneBy(['id_muni' => $muniId]);
        $muniName = $muni->getNomMuni();


        return $this->render('overview/index.html.twig', [
            'controller_name' => 'OverviewController',
            'user' => $user,
            'muniName' => $muniName,
        ]);
    }
}
