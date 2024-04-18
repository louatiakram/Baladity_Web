<?php

namespace App\Controller;

use App\Entity\enduser;
use App\Entity\muni;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/p', name: 'profile')]
    public function p(Request $request, ManagerRegistry $doctrine): Response
{
    $dataFromController1 = $this->forward('src\Controller\OverviewController.php::overview')->getContent();
    $dataFromController2 = $this->forward('src\Controller\EditProfileController.php::updateUser')->getContent();

    // Retrieving user ID from the session
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

    return $this->render('profile/index.html.twig', [
        'dataFromController1' => $dataFromController1,
        'dataFromController2' => $dataFromController2,
        'user' => $user,
        'muniName' => $muniName,
    ]);
}
}
