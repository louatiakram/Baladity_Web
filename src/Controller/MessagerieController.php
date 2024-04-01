<?php

namespace App\Controller;

use DateTime;
use App\Entity\messagerie;
use App\Form\MessagerieAdminType;
use App\Form\MessagerieModificationType;
use App\Repository\MessagerieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;


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
    $userId2 = 49;

    // Récupérer les messages entre les deux utilisateurs
    $messagesQuery = $messagerieRepository->findByUsers($id, $userId2);

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
