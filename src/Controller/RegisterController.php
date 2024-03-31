<?php

namespace App\Controller;

use App\Entity\enduser;
use App\Form\RegisterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(): Response
    {
        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
        ]);
    }
    
    
    #[Route('/Register', name: 'register_user')]
    public function registerUser(ManagerRegistry $doctrine, Request $request): Response
    {
        $user = new enduser();

        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_main');
        }

        return $this->render('register/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
