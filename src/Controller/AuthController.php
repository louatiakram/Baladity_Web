<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/a', name: 'app_auth')]
    public function index(): Response
    {

        $dataFromController1 = $this->forward('App\Controller\Controller1::someAction')->getContent();
        $dataFromController2 = $this->forward('App\Controller\Controller2::anotherAction')->getContent();
    

        return $this->render('auth/index.html.twig', [
            'controller_name' => 'AuthController',
            'dataFromController1' => $dataFromController1,
            'dataFromController2' => $dataFromController2,
        ]);
    }
}
