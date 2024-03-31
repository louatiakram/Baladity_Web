<?php

namespace App\Controller;

use App\Repository\MessagerieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessagerieController extends AbstractController
{
    #[Route('/messagerie', name: 'app_messagerie')]
    public function index(): Response
    {
        return $this->render('messagerie/index.html.twig', [
            'controller_name' => 'MessagerieController',
        ]);
    }

    #[Route('/messagerie/afficherMessagerie/{id}', name: 'afficherMessagerie')]
    public function afficherMessagerie(int $id,MessagerieRepository $messagerieRepository): Response
    {
        $userId2 = 49;
        // Récupérer les messages entre les deux utilisateurs
        $messages = $messagerieRepository->findByUsers($id, $userId2);

        return $this->render('messagerie/afficherMessagerie.html.twig', [
            'messages' => $messages,
        ]);
    }
}
