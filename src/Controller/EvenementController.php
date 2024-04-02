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
use DateTime;
use Doctrine\Persistence\ManagerRegistry;



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
        $imageFile = $form->get('imageEvent')->getData();
            if ($imageFile) {
                $fileName = uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('uploadsDirectory'), $fileName);
                    $evenement->setImageEvent($fileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload image.');
                    return $this->redirectToRoute('ajouter_evenement', ['id' => $id]);
                }
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
    public function modifierEvenement($id, Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $evenement = $entityManager->getRepository(Evenement::class)->find($id);

        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé avec l\'id : '.$id);
        }

        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('imageEvent')->getData();
            if ($imageFile) {
                $fileName = uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('uploadsDirectory'), $fileName);
                    $evenement->setImageEvent($fileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload image.');
                    return $this->redirectToRoute('modifier_evenement', ['id' => $id]);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été modifié avec succès.');

            return $this->redirectToRoute('evenement_list');
        }

        return $this->render('evenement/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/evenement/details/{id}', name: 'details_evenement')]
public function detailsEvenement($id, EntityManagerInterface $entityManager): Response
{
    $evenement = $entityManager->getRepository(Evenement::class)->find($id);

    if (!$evenement) {
        throw $this->createNotFoundException('Événement non trouvé avec l\'id : '.$id);
    }

    return $this->render('evenement/details.html.twig', [
        'evenement' => $evenement,
    ]);
}


}
