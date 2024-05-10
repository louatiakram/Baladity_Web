<?php

namespace App\Controller;

use DateTime;
use App\Entity\enduser;
use App\Entity\messagerie;
use App\Entity\reclamation;
use App\Form\MessagerieType;
use App\Form\MessagerieAdminType;
use Symfony\Component\Form\FormError;
use App\Form\MessagerieModificationType;
use App\Repository\MessagerieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
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
public function afficherMessagerie(int $id, MessagerieRepository $messagerieRepository, PaginatorInterface $paginator, Request $request): Response
{
    $userId = $request->getSession()->get('user_id');
    //get user
            $userRepository = $doctrine->getRepository(enduser::class);
            $users = $userRepository->findOneBy(['id_user' => $userId]);
    // Récupérer les messages entre les deux utilisateurs
    $messagesQuery = $messagerieRepository->findByUsers($id, $userId);

    // Paginer les résultats
    $messages = $paginator->paginate(
        $messagesQuery, // Doctrine Query object
        $request->query->getInt('page', 1), // Numéro de page actuel
        10 // Nombre d'éléments par page
    );

    return $this->render('messagerie/afficherMessagerie.html.twig', [
        'messages' => $messages,
        'id' => $id, // Passer l'identifiant de l'utilisateur à la vue
    ]);
}
#[Route('/messagerie/afficherMessagerieF', name: 'afficherMessagerieF')]
public function afficherMessagerieF(MessagerieRepository $messagerieRepository, Request $request,ManagerRegistry $doctrine): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $userId = $request->getSession()->get('user_id');
    //get user
            $userRepository = $doctrine->getRepository(enduser::class);
            $users = $userRepository->findOneBy(['id_user' => $userId]);
    // Récupérer l'utilisateur actuellement connecté (ID statique)
   
    $currentUser = $entityManager->getRepository(enduser::class)->find($userId);

    // ID de l'autre utilisateur (statique)
    $otherUserId = 149; // Exemple d'ID utilisateur
    $currentUser2 = $entityManager->getRepository(enduser::class)->find($otherUserId);


    // Récupérer les messages entre les deux utilisateurs
    $messages = $messagerieRepository->findByUsers($userId, $otherUserId);

    // Créer une nouvelle entité Messagerie
    $messagerie = new Messagerie();
    $form = $this->createForm(MessagerieType::class, $messagerie);
    $form->handleRequest($request);

    // Vérifier si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Add this line to ensure the form submission is reaching here
        // Définir l'utilisateur actuellement connecté comme expéditeur du message
        $messagerie->setSenderIdMessage($currentUser);
        $messagerie->setReceiverIdMessage($currentUser2);
        $messagerie->setTypeMessage('text');
        // Définir l'utilisateur destinataire du message
        
        // Vous devrez implémenter cette partie selon votre logique métier

        // Définir la date du message
        $messagerie->setDateMessage(new \DateTime());

        // Persist and flush the Messagerie entity
        $entityManager->persist($messagerie);
        $entityManager->flush();

        // Ajouter un message flash pour confirmer que le message a été envoyé avec succès
        $this->addFlash('success', 'Votre message a été envoyé avec succès.');

        // Redirection vers la même route pour rafraîchir les messages
       return $this->redirectToRoute('afficherMessagerieF');
    }

    return $this->render('messagerie/afficherMessagerieF.html.twig', [
        'form' => $form->createView(),
        'messages' => $messages,
        'currentUser' => $currentUser,
        'user' => $users,
    ]);
}
#[Route('/messagerie/afficherMessagerieFA/{id}', name: 'afficherMessagerieFA')]
public function afficherMessagerieFA($id,MessagerieRepository $messagerieRepository, Request $request,ManagerRegistry $doctrine): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $userId = $request->getSession()->get('user_id');
    //get user
            $userRepository = $doctrine->getRepository(enduser::class);
            $users = $userRepository->findOneBy(['id_user' => $userId]);
    // Récupérer l'utilisateur actuellement connecté (ID statique)
    $reclamation = $entityManager->getRepository(reclamation::class)->find($id);
    $receiverId=$reclamation->getIdUser()->getIdUser();
    $receiver=$entityManager->getRepository(enduser::class)->find($receiverId);

    // ID de l'autre utilisateur (statique)
    $otherUserId = 149; // Exemple d'ID utilisateur
    $currentUser2 = $entityManager->getRepository(enduser::class)->find($otherUserId);


    // Récupérer les messages entre les deux utilisateurs
    $messages = $messagerieRepository->findByUsers($otherUserId, $receiverId);

    // Créer une nouvelle entité Messagerie
    $messagerie = new Messagerie();
    $form = $this->createForm(MessagerieType::class, $messagerie);
    $form->handleRequest($request);

    // Vérifier si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Add this line to ensure the form submission is reaching here
        // Définir l'utilisateur actuellement connecté comme expéditeur du message
        $messagerie->setSenderIdMessage($currentUser2);
        $messagerie->setReceiverIdMessage($receiver);
        $messagerie->setTypeMessage('text');
        // Définir l'utilisateur destinataire du message
        
        // Vous devrez implémenter cette partie selon votre logique métier

        // Définir la date du message
        $messagerie->setDateMessage(new \DateTime());

        // Persist and flush the Messagerie entity
        $entityManager->persist($messagerie);
        $entityManager->flush();

        // Ajouter un message flash pour confirmer que le message a été envoyé avec succès
        $this->addFlash('success', 'Votre message a été envoyé avec succès.');

        // Redirection vers la même route pour rafraîchir les messages
       return $this->redirectToRoute('afficherReclamationFA');
    }

    return $this->render('messagerie/afficherMessagerieFA.html.twig', [
        'form' => $form->createView(),
        'messages' => $messages,
        'currentUser' => $currentUser2,
        'user' => $users,
    ]);
}
#[Route('/messagerie/modifierMessagerie/{id}', name: 'modifierMessagerie')]
public function modifierMessagerie(int $id, Request $request): Response
{
    $entityManager = $this->getDoctrine()->getManager();

    // Trouver la messagerie à modifier
    $messagerie = $entityManager->getRepository(Messagerie::class)->find($id);

    // Vérifier si la messagerie existe
    if (!$messagerie) {
        throw $this->createNotFoundException('Messagerie not found');
    }

    // Créer le formulaire pour modifier la messagerie
    $form = $this->createForm(MessagerieModificationType::class, $messagerie);
    $form->handleRequest($request);

    // Vérifier si le formulaire a été soumis et est valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer les valeurs de date et d'heure du formulaire
        $datePickerValue = $request->request->get('datePicker');
        $timePickerValue = $request->request->get('timePicker');
        
        // Concaténer la date et l'heure pour former une datetime complète
        $dateTimeString = $datePickerValue . ' ' . $timePickerValue;
        
        // Convertir la chaîne en objet DateTime
        $dateMessage = \DateTime::createFromFormat('Y-m-d H:i', $dateTimeString);
        
        // Vérifier si la conversion a réussi
        if ($dateMessage instanceof \DateTime) {
            // Définir la valeur de date_message dans l'entité messagerie
            $messagerie->setDateMessage($dateMessage);
        
            // Mettre à jour la messagerie dans la base de données
            $entityManager->flush();
        
            // Redirection vers la page d'affichage des messages
            return $this->redirectToRoute('afficherMessagerie', ['id' => $messagerie->getSenderIdMessage()->getIdUser()]);
        } else {
            // Si la conversion a échoué, ajouter une erreur au formulaire
            $form->addError(new FormError('Invalid date or time format'));
        }
    }

    // Rendre le formulaire et la messagerie à modifier dans le modèle Twig
    return $this->render('messagerie/modifierMessagerie.html.twig', [
        'form' => $form->createView(),
        'messagerie' => $messagerie,
    ]);
}
#[Route('/messagerie/modifierMessagerieF/{id}', name: 'modifierMessagerieF')]
public function modifierMessagerieF(int $id, Request $request): Response
{
    $entityManager = $this->getDoctrine()->getManager();

    // Trouver le message à modifier
    $message = $entityManager->getRepository(Messagerie::class)->find($id);

    // Vérifier si le message existe
    if (!$message) {
        throw $this->createNotFoundException('Message not found');
    }

    // Créer le formulaire pour modifier le message
    $form = $this->createForm(MessagerieType::class, $message);
    $form->handleRequest($request);

    // Vérifier si le formulaire a été soumis et est valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Mettre à jour le message dans la base de données
        $entityManager->flush();

        // Redirection vers une autre page ou un affichage approprié
        return $this->redirectToRoute('afficherMessagerieF');
    }

    // Rendre le formulaire et le message à modifier dans le modèle Twig
    return $this->render('messagerie/modifierMessagerieF.html.twig', [
        'form' => $form->createView(),
        'message' => $message,
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
    return $this->redirectToRoute('afficherMessagerie', ['id' => $message->getSenderIdMessage()->getIdUser()]);
}
#[Route('/messagerie/supprimerMessagerieF/{id}', name: 'supprimerMessagerieF')]
public function supprimerMessagerieF(int $id, MessagerieRepository $messagerieRepository): Response
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
    return $this->redirectToRoute('afficherMessagerieF');
}
#[Route('/messagerie/ajouterMessage', name: 'ajouterMessage')]
public function ajouterMessage(Request $request): Response
{
    $messagerie = new messagerie();

    // Récupérer l'utilisateur actuellement connecté
    $currentUser = $this->getUser();

    // Définir l'utilisateur actuel comme expéditeur du message
    $messagerie->setSenderIdMessage($currentUser);

    $form = $this->createForm(MessagerieAdminType::class, $messagerie);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $datePickerValue = $request->request->get('datePicker');
        $timePickerValue = $request->request->get('timePicker');
        
        // Créer une chaîne de date et d'heure au format complet (YYYY-MM-DD HH:MM:SS)
        $dateTimeString = $datePickerValue . ' ' . $timePickerValue;
        
        // Convertir la chaîne en objet DateTime
        $dateMessage = \DateTime::createFromFormat('Y-m-d H:i', $dateTimeString);
        
        // Vérifier si la conversion a réussi
        if ($dateMessage instanceof \DateTime) {
            // Définir la valeur de date_message dans l'entité messagerie
            $messagerie->setDateMessage($dateMessage);
        
            // Persister l'entité messagerie
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($messagerie);
            $entityManager->flush();
        
            // Redirection vers la page d'affichage des messages
            return $this->redirectToRoute('afficherReclamation');
        }
    }

    return $this->render('messagerie/ajouterMessagerie.html.twig', [
        'form' => $form->createView(),
    ]);
}

}


