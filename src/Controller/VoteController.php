<?php

namespace App\Controller;

use App\Entity\vote; 
use App\Form\VoteType;
use App\Entity\enduser;
use Doctrine\ORM\Query\Expr;
use App\Repository\VoteRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;  // Import the correct class


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
    public function list(Request $request, VoteRepository $repository, PaginatorInterface $paginator): Response
    {
        $query = $request->query->get('query');
        $orderBy = ['date_SV' => 'ASC']; // Order by submission date in ascending order
        $queryBuilder = $repository->createQueryBuilder('v');
        if ($query) {
            // If a search query is provided, filter votes based on the description
            $queryBuilder->where('v.desc_E LIKE :query')
                         ->setParameter('query', '%'.$query.'%');
        }
        // Fetch votes using query builder
        $queryBuilder->orderBy('v.date_SV', 'ASC'); // Sort votes by submission date in ascending order
        $votes = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1), // Current page number, default is 1
            5 // Number of items per page
        );
        return $this->render('vote/list.html.twig', [
            'votes' => $votes,
            'query' => $query, // Pass the query to the template for displaying in the search bar
        ]);
    }
    
    #[Route('/vote/ajouter', name: 'ajouter_vote')]
public function add(Request $request,ManagerRegistry $doctrine): Response
{
    $entityManager = $this->getDoctrine()->getManager();

    // Create a new Vote object
    $vote = new vote();
    $userId = $request->getSession()->get('user_id');
    //get user
            $userRepository = $doctrine->getRepository(enduser::class);
            $users = $userRepository->findOneBy(['id_user' => $userId]);
    $vote->setIdUser($userId);
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

#[Route('/vote/ajouterFront', name: 'ajouter_voteFront')]
public function addFront(Request $request,ManagerRegistry $doctrine): Response
{
    $entityManager = $this->getDoctrine()->getManager();

    // Create a new Vote object
    $vote = new vote();
    $userId = $request->getSession()->get('user_id');
    //get user
            $userRepository = $doctrine->getRepository(enduser::class);
            $users = $userRepository->findOneBy(['id_user' => $userId]);
    $vote->setIdUser($userId);
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
        $this->addFlash('success', 'Proposition added successfully.');

        // Redirect to the vote list page
        return $this->redirectToRoute('evenement_listCitoyen');
    }

    return $this->render('vote/ajouterFront.html.twig', [
        'form' => $form->createView(),
        'user' => $users
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

#[Route('/vote/stats', name: 'app_vote')]
public function stats(VoteRepository $voteRepository): Response
{
     // Fetch monthly and yearly vote statistics
     $monthlyYearlyVotes = $voteRepository->getMonthlyYearlyVotes();

     // Fetch total number of votes
     $totalVotes = $voteRepository->getTotalVotes();

     // Fetch latest votes (last 24 hours)
     $latestVotes = $voteRepository->getLatestVotes();

    return $this->render('vote/stats.html.twig', [
        'monthlyYearlyVotes' => $monthlyYearlyVotes,
        'totalVotes' => $totalVotes,
        'latestVotes' => $latestVotes,
    ]);
}
}





