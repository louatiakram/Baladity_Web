<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\evenement;
use App\Repository\EvenementRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;  // Import the correct class
use App\Form\EvenementType;


class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'app_evenement')]
    public function index(): Response
    {
        return $this->render('evenement/index.html.twig', [
            'controller_name' => 'EvenementController',
        ]);
    }
    
    #[Route('/evenement/list', name: 'evenement_list')]
    public function list(Request $request, EvenementRepository $repository): Response
    {
        $query = $request->query->get('query');

        // If a search query is provided, filter events based on the name
        if ($query) {
            $evenements = $repository->findByNomE($query); // Assuming findByNomE is a custom method in your repository
        } else {
            // If no search query is provided, fetch all events
            $evenements = $repository->findAll();
        }

        return $this->render('evenement/list.html.twig', [
            'evenements' => $evenements,
            'query' => $query, // Pass the query to the template for displaying in the search bar
        ]);
    }

    #[Route('/evenement/ajouter', name: 'ajouter_evenement')]
public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
{
    // Create a new Evenement instance
    $evenement = new Evenement();
    
    // Set the static user ID (48 in this case)
    $evenement->setIdUser(48);

    // Handle form submission
    $form = $this->createForm(EvenementType::class, $evenement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle image upload and persist its filename to the database
        $imageFile = $form->get('imageEvent')->getData();
        if ($imageFile instanceof UploadedFile) {
            $fileName = uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move($this->getParameter('uploadsDirectory'), $fileName);
                $evenement->setImageEvent($fileName);
            } catch (FileException $e) {
                // Handle file upload exception if necessary
                $this->addFlash('error', 'Failed to upload image.');
                return $this->redirectToRoute('ajouter_evenement'); // Redirect back to the form
            }
        } else {
            // Set a default image path if no image is uploaded
            $evenement->setImageEvent('default_image.jpg'); // Adjust the default image path as needed
        }

        // Persist the evenement object to the database
        $entityManager->persist($evenement);
        $entityManager->flush();

        // Redirect to a success page or route
        return $this->redirectToRoute('evenement_list');
    }

    // Render the form view
    return $this->render('evenement/ajouter.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route('/evenement/supprimer/{id}', name: 'supprimer_evenement')]
public function supprimerEvenement($id, EntityManagerInterface $entityManager): Response
{
    $evenement = $entityManager->getRepository(Evenement::class)->find($id);

    if (!$evenement) {
        throw $this->createNotFoundException('Événement non trouvé avec l\'id : '.$id);
    }

    $entityManager->remove($evenement);
    $entityManager->flush();

    // Ajoutez un message flash pour confirmer la suppression
    $this->addFlash('success', 'L\'événement a été supprimé avec succès.');

    return $this->redirectToRoute('evenement_list');
}

#[Route('/evenement/modifier/{id}', name: 'modifier_evenement')]
public function modifierEvenement($id, Request $request, EntityManagerInterface $entityManager): Response
{
    $evenement = $entityManager->getRepository(Evenement::class)->find($id);

    if (!$evenement) {
        throw $this->createNotFoundException('Événement non trouvé avec l\'id : '.$id);
    }

    // Créer un formulaire pour modifier l'événement
    $form = $this->createForm(EvenementType::class, $evenement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Persist the updated evenement object to the database
        $entityManager->flush();

        // Ajouter un message flash pour confirmer la modification
        $this->addFlash('success', 'L\'événement a été modifié avec succès.');

        return $this->redirectToRoute('evenement_list');
    }

    return $this->render('evenement/modifier.html.twig', [
        'form' => $form->createView(),
    ]);
}

}
