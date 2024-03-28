<?php

namespace App\Controller;

use DateTime;
use App\Entity\enduser;
use App\Entity\reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Component\Pager\PaginatorInterface;


class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'controller_name' => 'ReclamationController',
        ]);
    }

    #[Route('/reclamation/typeReclamation', name: 'typeReclamation')]
    public function typeReclamation(): Response
    {
        return $this->render('reclamation/typeReclamation.html.twig');
    }

    #[Route('/reclamation/ajouterReclamation/{cas}', name: 'ajouterReclamation')]
    public function ajouterReclamation(Request $request, $cas,SessionInterface $session): Response
    {
        // Créer une nouvelle instance de l'entité Reclamation
        $reclamation = new Reclamation();

        // Récupérer l'identifiant de l'utilisateur (à remplacer par votre logique d'identification)
        $userId = 48; 
        $user = $this->getDoctrine()->getRepository(EndUser::class)->find($userId);
    
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        $reclamation->setIdUser($user);
        $reclamation->setIdMuni($user->getIdMuni());  
        $reclamation->setDateReclamation(new DateTime());
        $reclamation->setStatusReclamation('non traité');
    
        // Déterminer dynamiquement les choix pour type_reclamation en fonction de $cas
        if ($cas == 1) {
            $choices = [
                'Urgences médicales' => 'Urgences médicales',
                'Incendies' => 'Incendies',
                'Fuites de gaz' => 'Fuites de gaz',
                'Inondations' => 'Inondations',
                'Défaillances des infrastructures critiques' => 'Défaillances des infrastructures critiques',
            ];
        } else {
            $choices = [
                'Réparations de voirie' => 'Réparations de voirie',
                'Collecte des déchets' => 'Collecte des déchets',
                'Environnement' => 'Environnement',
                'Aménagement paysager' => 'Aménagement paysager',
                'Problèmes de logement' => 'Problèmes de logement',
                'Services municipaux' => 'Services municipaux',
            ];
        }

        // Créer le formulaire en passant les choix dynamiques pour le type_reclamation
        $form = $this->createForm(ReclamationType::class, $reclamation, [
            'type_reclamation_choices' => $choices,
        ]);
        
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Set the image_a field
            $image = $form->get('image_reclamation')->getData();
            if ($image) {
                // Handle image upload and persist its filename to the database
                $fileName = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move($this->getParameter('uploadsDirectory'), $fileName);
                    $reclamation->setImageReclamation($fileName);
                } catch (FileException $e) {
                    // Handle the exception if file upload fails
                    // For example, log the error or display a flash message
                }
            }
        
            // Get the entity manager
            $em = $this->getDoctrine()->getManager();
        
            // Persist the reclamation object to the database
            $em->persist($reclamation);
            $em->flush();
           // Ajout du message flash
           $this->addFlash('success', 'La réclamation a été ajoutée avec succès.');
           // Redirection vers la page d'affichage des réclamations
           return $this->redirectToRoute('afficherReclamation');
        }
        return $this->render('reclamation/ajouterReclamation.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reclamation/afficherReclamation', name: 'afficherReclamation')]
public function afficherReclamation(Request $request, ReclamationRepository $repository, PaginatorInterface $paginator): Response
{
    $query = $request->query->get('query');

    // Fetch the current page number from the query parameters
    $currentPage = $request->query->getInt('page', 1);

    // If a search query is provided, filter tasks based on the title
    if ($query) {
        $queryBuilder = $repository->createQueryBuilder('r')
            ->where('r.sujetReclamation LIKE :query')
            ->setParameter('query', "%$query%");
    } else {
        // If no search query is provided, fetch all tasks
        $queryBuilder = $repository->createQueryBuilder('r');
    }

    // Paginate the results using the paginator service
    $reclamations = $paginator->paginate(
        $queryBuilder->getQuery(), // Doctrine Query object
        $currentPage, // Current page number
        5 // Number of items per page
    );

    return $this->render('reclamation/afficherReclamation.html.twig', [
        'reclamations' => $reclamations,
        'query' => $query,
    ]);
}

 #[Route('/reclamation/supprimerReclamation/{i}', name: 'supprimerReclamation')]
    public function deleteA($i, ReclamationRepository $rep, ManagerRegistry $doctrine): Response
    {
        $reclamation = $rep->find($i);
    
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation not found');
        }
    
        $em = $doctrine->getManager();
        $em->remove($reclamation);
        $em->flush();
    
        // Redirect to a success page or return a response as needed
        // For example:
        return $this->redirectToRoute('afficherReclamation');
    }
}

