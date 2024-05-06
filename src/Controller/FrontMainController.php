<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontMainController extends AbstractController
{
    #[Route('/main', name: 'app_front_main')]
    public function index(): Response
    {
        return $this->render('base-front.html.twig', [
            'controller_name' => 'FrontMainController',
        ]);
    }
}
