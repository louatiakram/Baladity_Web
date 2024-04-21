<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\VoteRepository;
use App\Entity\vote; 
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;  // Import the correct class
use Symfony\Component\Form\FormFactoryInterface;
use App\Form\VoteType;

class VoteController extends AbstractController
{
    #[Route('/vote', name: 'app_vote')]
    public function index(): Response
    {
        return $this->render('vote/index.html.twig', [
            'controller_name' => 'VoteController',
        ]);
    }

    #[Route('/vote/list', name: 'vote_list')]
    public function list(Request $request, VoteRepository $repository): Response
    {
        $query = $request->query->get('query');

        // Trier les votes par date de soumission
        $orderBy = ['date_SV' => 'ASC']; // Ordonner par date de soumission de manière ascendante

        // Si une requête de recherche est fournie, filtrer les votes en fonction de la description
        if ($query) {
            $votes = $repository->findByDescE($query, $orderBy); // Supposons que findByDescE est une méthode personnalisée dans votre repository
        } else {
            // Si aucune requête de recherche n'est fournie, récupérer tous les votes triés par date de soumission
            $votes = $repository->findBy([], $orderBy);
        }

        return $this->render('vote/list.html.twig', [
            'votes' => $votes,
            'query' => $query, // Passer la requête au modèle pour l'afficher dans la barre de recherche
        ]);
    }

    #[Route('/vote/ajouter', name: 'ajouter_vote')]
public function add(Request $request): Response
{
    $entityManager = $this->getDoctrine()->getManager();

    // Create a new Vote object
    $vote = new vote();
    $vote->setIdUser(50);
    // Set the date_SV field to the current date
    $vote->setDateSV(new \DateTime());

    // Handle form submission
    $form = $this->createForm(VoteType::class, $vote);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Persist the vote object
        $entityManager->persist($vote);
        $entityManager->flush();

        // Flash message for success
        $this->addFlash('success', 'Vote added successfully.');

        // Redirect to the vote list page
        return $this->redirectToRoute('vote_list');
    }

    return $this->render('vote/ajouter.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/vote/modifier/{id}', name: 'modifier_vote')]
    public function update($id, VoteRepository $repository, Request $request, ManagerRegistry $doctrine, FormFactoryInterface $formFactory): Response
    {
        // Find the vote entity by id
        $vote = $repository->find($id);

        // Create the form
        $form = $formFactory->create(VoteType::class, $vote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            // Redirect to the vote list page
            return $this->redirectToRoute('vote_list');
        }

        return $this->render('vote/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/vote/supprimer/{id}', name: 'supprimer_vote')]
public function delete($id, VoteRepository $repository, ManagerRegistry $doctrine): Response
{
    // Find the vote entity by id
    $vote = $repository->find($id);

    // If the vote entity doesn't exist, redirect back to the vote list
    if (!$vote) {
        return $this->redirectToRoute('vote_list');
    }

    // Get the entity manager
    $entityManager = $doctrine->getManager();

    // Remove the vote entity
    $entityManager->remove($vote);
    $entityManager->flush();

    // Flash message for success
    $this->addFlash('success', 'Vote deleted successfully.');

    // Redirect to the vote list page
    return $this->redirectToRoute('vote_list');
}

#[Route('/vote/details/{id}', name: 'details_vote')]
public function detailsVote($id, EntityManagerInterface $entityManager): Response
{
    $vote = $entityManager->getRepository(vote::class)->find($id);

    if (!$vote) {
        throw $this->createNotFoundException('Proposition not found with id: ' . $id);
    }

    return $this->render('vote/details.html.twig', [
        'vote' => $vote,
    ]);
}
}
