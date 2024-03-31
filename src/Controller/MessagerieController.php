<?php

namespace App\Controller;

use App\Entity\messagerie;
use App\Form\MessagerieModificationType;
use App\Repository\MessagerieRepository;
use Symfony\Component\HttpFoundation\Request;
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
    #[Route('/messagerie/modifierMessagerie/{id}', name: 'modifierMessagerie')]
    public function modifierMessagerie(int $id, Request $request): Response
{
    $entityManager = $this->getDoctrine()->getManager();

    // Trouver la messagerie à modifier
    $messagerie = $entityManager->getRepository(messagerie::class)->find($id);

    // Vérifier si la messagerie existe
    if (!$messagerie) {
        throw $this->createNotFoundException('Messagerie not found');
    }

    // Créer le formulaire pour modifier la messagerie
    $form = $this->createForm(MessagerieModificationType::class, $messagerie);
    $form->handleRequest($request);

    // Vérifier si le formulaire a été soumis et est valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Traiter la soumission du formulaire
        $data = $form->getData();

        // Mettre à jour les champs nécessaires de l'objet de messagerie
        $messagerie->setContenuMessage($data->getContenuMessage());
        $messagerie->setTypeMessage($data->getTypeMessage());
        $messagerie->setDateMessage($data->getDateMessage());

        // Enregistrer l'objet de messagerie modifié dans la base de données
        $entityManager->flush();

        // Rediriger vers une page de succès ou afficher un message de succès
        // Par exemple :
        return $this->redirectToRoute('afficherReclamation');
    }

    // Rendre le formulaire et la messagerie à modifier dans le modèle Twig
    return $this->render('messagerie/modifierMessagerie.html.twig', [
        'form' => $form->createView(),
        'messagerie' => $messagerie,
    ]);
}

#[Route('/messagerie/supprimerMessagerie/{id}', name: 'supprimerMessagerie')]
public function supprimerMessagerie(int $id, MessagerieRepository $messagerieRepository): Response
{
    // Récupérer le message à supprimer
    $message = $messagerieRepository->find($id);

    // Vérifier si le message existe
    if (!$message) {
        throw $this->createNotFoundException('Message not found');
    }

    // Supprimer le message de la base de données
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($message);
    $entityManager->flush();

    // Rediriger vers la page d'affichage des messages ou une autre page appropriée
    return $this->redirectToRoute('afficherReclamation');
}
}
