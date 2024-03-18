<?php

namespace App\Controller;

use App\Entity\tache;
use App\Form\TacheType;
use App\Entity\enduser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TacheController extends AbstractController
{
    #[Route('/tache', name: 'app_tache')]
    public function index(): Response
    {
        return $this->render('tache/index.html.twig', [
            'controller_name' => 'TacheController',
        ]);
    }

    #[Route('/tache/ajouter', name: 'tache_ajouter')]
    public function ajouter(Request $request): Response
    {
// Fetch the user entity

        $userId = 50; // Assuming the user ID is 50

        // Fetch the enduser entity from the database based on $userId
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }


        $tache = new tache();
        $tache->setIdUser($user);
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the task to the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tache);
            $entityManager->flush();

            // Redirect to the index page or wherever you want
            return $this->redirectToRoute('app_tache');
        }

        return $this->render('tache/ajoutertache.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
