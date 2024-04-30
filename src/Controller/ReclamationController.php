<?php

namespace App\Controller;

use DateTime;
use App\Entity\enduser;
use App\Entity\reclamation;
use App\Form\ReclamationType;
use App\Form\ReclamationAdminType;
use App\Form\ReclamationModifierType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReclamationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


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
        return $this->render('reclamation/typeReclamationF.html.twig');
    }

    #[Route('/reclamation/ajouterReclamation/{cas}', name: 'ajouterReclamation')]
    public function ajouterReclamation(Request $request, $cas,SessionInterface $session): Response
    {
        // Créer une nouvelle instance de l'entité Reclamation
        $reclamation = new Reclamation();
    
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
        $form = $this->createForm(ReclamationAdminType::class, $reclamation, [
        'type_reclamation_choices' => $choices,
        ]);
        
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
             // Récupérer l'utilisateur à partir du formulaire
        $user = $form->get('id_user')->getData();

        // Assurez-vous que l'utilisateur est valide
        if (!$user) {
            throw new \Exception('Utilisateur non spécifié');
        }

        // Mettre à jour l'utilisateur de la réclamation
        $reclamation->setIdUser($user);
        $reclamation->setIdMuni($user->getIdMuni());  
        $reclamation->setDateReclamation(new DateTime());
        $reclamation->setStatusReclamation('non traité');
            // Set the image_a field
            $image = $form->get('image_reclamation')->getData();
            if ($image) {
                // Gérer le téléchargement de l'image et enregistrer son nom de fichier dans la base de données
                $fileName = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move($this->getParameter('uploads_directory'), $fileName);
                    $reclamation->setImageReclamation($fileName);
                } catch (FileException $e) {
                    // Gérer l'exception si le téléchargement du fichier échoue
                    // Par exemple, journaliser l'erreur ou afficher un message flash
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

    #[Route('/reclamation/ajouterReclamationF/{cas}', name: 'ajouterReclamationF')]
public function ajouterReclamationF(Request $request, $cas): Response
{
    // Créer une nouvelle instance de l'entité Reclamation
    $reclamation = new Reclamation();

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
        // Récupérer l'utilisateur à partir du formulaire
        $userId = 48;
        $user = $this->getDoctrine()->getRepository(EndUser::class)->find($userId);


        // Assurez-vous que l'utilisateur est valide
        if (!$user) {
            throw new \Exception('Utilisateur non spécifié');
        }

        // Mettre à jour l'utilisateur de la réclamation
        $reclamation->setIdUser($user);
        $reclamation->setIdMuni($user->getIdMuni());  
        $reclamation->setDateReclamation(new DateTime());
        $reclamation->setStatusReclamation('non traité');

        // Set the image_a field
        $image = $form->get('image_reclamation')->getData();
        if ($image) {
            // Gérer le téléchargement de l'image et enregistrer son nom de fichier dans la base de données
            $fileName = uniqid().'.'.$image->guessExtension();
            try {
                $image->move($this->getParameter('uploads_directory'), $fileName);
                $reclamation->setImageReclamation($fileName);
            } catch (FileException $e) {
                // Gérer l'exception si le téléchargement du fichier échoue
                // Par exemple, journaliser l'erreur ou afficher un message flash
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
       // return $this->redirectToRoute('afficherReclamation');
    }

    return $this->render('reclamation/ajouterReclamationF.html.twig', [
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
#[Route('/reclamation/afficherReclamationF', name: 'afficherReclamationF')]
public function afficherReclamationF(Request $request, ReclamationRepository $repository, PaginatorInterface $paginator): Response
{
    $userId = 48;

    // Récupérer les réclamations de l'utilisateur actuel
    $reclamations = $repository->findReclamationsByUserId($userId);

    return $this->render('reclamation/afficherReclamationF.html.twig', [
        'reclamations' => $reclamations,
    ]);
}
#[Route('/reclamation/afficherReclamationFA', name: 'afficherReclamationFA')]
public function afficherReclamationFA(Request $request, ReclamationRepository $repository, PaginatorInterface $paginator): Response
{
    // Récupérer toutes les réclamations
    $reclamations = $repository->findAll();

    return $this->render('reclamation/afficherReclamationA.html.twig', [
        'reclamations' => $reclamations,
    ]);
}


#[Route('/reclamation/filtrerParDate', name: 'filtrerParDate')]
public function filtrerParDate(Request $request, ReclamationRepository $repository, SessionInterface $session): Response
{
    $sortingState = $session->get('sorting_state', 'normal');
    
    if ($sortingState === 'normal') {
        $userId = 48; // Utilisateur pour lequel vous souhaitez filtrer les réclamations par date
        $reclamations = $repository->findReclamationsByDate($userId);
        $session->set('sorting_state', 'sorted');
    } else {
        $userId = 48; // Utilisateur pour lequel vous souhaitez filtrer les réclamations par date
        $reclamations = $repository->findReclamationsByUserId($userId);
        $session->set('sorting_state', 'normal');    }

    return $this->render('reclamation/afficherReclamationF.html.twig', [
        'reclamations' => $reclamations,
        'sorting_state' => $sortingState,
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
    #[Route('/reclamation/supprimerReclamationF/{i}', name: 'supprimerReclamationF')]
    public function deleteAF($i, ReclamationRepository $rep, ManagerRegistry $doctrine): Response
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
        return $this->redirectToRoute('afficherReclamationF');
    }

    #[Route('/reclamation/modifierReclamation/{id}', name: 'modifierReclamation')]
    public function modifierReclamation($id, ManagerRegistry $doctrine, Request $request): Response
    {
        // Récupérer l'entity manager
        $entityManager = $doctrine->getManager();
        
        // Trouver la réclamation à modifier
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);

        // Vérifier si la réclamation existe
        if (!$reclamation) {
            throw $this->createNotFoundException('Reclamation not found');
        }

        // Créer le formulaire pour modifier la réclamation
        $form = $this->createForm(ReclamationModifierType::class, $reclamation);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Traiter la soumission du formulaire
            $reclamation->setDateReclamation(new DateTime());
            
            // Récupérer le fichier de l'image de réclamation
            $image = $form->get('image_reclamation')->getData();
            if ($image) {
                // Gérer le téléchargement de l'image et enregistrer son nom de fichier dans la base de données
                $fileName = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move($this->getParameter('uploads_directory'), $fileName);
                    $reclamation->setImageReclamation($fileName);
                } catch (FileException $e) {
                    // Gérer l'exception si le téléchargement du fichier échoue
                    // Par exemple, journaliser l'erreur ou afficher un message flash
                }
            }

            // Enregistrer l'objet de réclamation modifié dans la base de données
            $entityManager->flush();

            // Rediriger vers une page de succès ou afficher un message de succès
            // Par exemple :
            return $this->redirectToRoute('afficherReclamation');
        }

        // Rendre le formulaire et la réclamation à modifier dans le modèle Twig
        return $this->render('reclamation/modifierReclamation.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }
    #[Route('/reclamation/modifierReclamationF/{id}', name: 'modifierReclamationF')]
    public function modifierReclamationF($id, ManagerRegistry $doctrine, Request $request): Response
    {
        // Récupérer l'entity manager
        $entityManager = $doctrine->getManager();
        
        // Trouver la réclamation à modifier
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);

        // Vérifier si la réclamation existe
        if (!$reclamation) {
            throw $this->createNotFoundException('Reclamation not found');
        }

        // Créer le formulaire pour modifier la réclamation
        $form = $this->createForm(ReclamationModifierType::class, $reclamation);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Traiter la soumission du formulaire
            $reclamation->setDateReclamation(new DateTime());
            
            // Récupérer le fichier de l'image de réclamation
            $image = $form->get('image_reclamation')->getData();
            if ($image) {
                // Gérer le téléchargement de l'image et enregistrer son nom de fichier dans la base de données
                $fileName = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move($this->getParameter('uploads_directory'), $fileName);
                    $reclamation->setImageReclamation($fileName);
                } catch (FileException $e) {
                    // Gérer l'exception si le téléchargement du fichier échoue
                    // Par exemple, journaliser l'erreur ou afficher un message flash
                }
            }

            // Enregistrer l'objet de réclamation modifié dans la base de données
            $entityManager->flush();

            // Rediriger vers une page de succès ou afficher un message de succès
            // Par exemple :
            return $this->redirectToRoute('afficherReclamationF');
        }

        // Rendre le formulaire et la réclamation à modifier dans le modèle Twig
        return $this->render('reclamation/modifierReclamationF.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/reclamation/detailReclamation/{id}', name: 'detailReclamation')]
    public function detailReclamation($id, ReclamationRepository $rep): Response
    {
        $reclamation=$rep->find($id);
        return $this->render('reclamation/detailReclamation.html.twig', [
            'reclamation' => $reclamation,
        ]);

    }
    #[Route('/reclamation/detailReclamationF/{id}', name: 'detailReclamationF')]
    public function detailReclamationF($id, ReclamationRepository $rep): Response
    {
        $reclamation=$rep->find($id);
        return $this->render('reclamation/detailReclamationF.html.twig', [
            'reclamation' => $reclamation,
        ]);

    }

    #[Route('/reclamation/detailReclamationFA/{id}', name: 'detailReclamationFA')]
    public function detailReclamationFA($id, ReclamationRepository $rep): Response
    {
        $reclamation=$rep->find($id);
        return $this->render('reclamation/detailReclamationFA.html.twig', [
            'reclamation' => $reclamation,
        ]);

    }
    
    #[Route('/reclamation/modifierStatutD/{id}', name: 'modifierStatutD')]
    public function modifierStatutD($id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer la réclamation à modifier
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);

        // Vérifier si la réclamation existe
        if (!$reclamation) {
            throw $this->createNotFoundException('Reclamation not found');
        }

        // Modifier le statut de la réclamation en "Non traité"
        $reclamation->setStatusReclamation('traité');

        // Enregistrer la réclamation modifiée dans la base de données
        $entityManager->flush();

        // Rediriger vers la même route de détail de réclamation avec le même ID
        return $this->redirectToRoute('detailReclamationFA', ['id' => $id]);
    }
    #[Route('/reclamation/modifierStatutE/{id}', name: 'modifierStatutE')]
    public function modifierStatutE($id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer la réclamation à modifier
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);

        // Vérifier si la réclamation existe
        if (!$reclamation) {
            throw $this->createNotFoundException('Reclamation not found');
        }

        // Modifier le statut de la réclamation en "Non traité"
        $reclamation->setStatusReclamation('en cours');

        // Enregistrer la réclamation modifiée dans la base de données
        $entityManager->flush();

        // Rediriger vers la même route de détail de réclamation avec le même ID
        return $this->redirectToRoute('detailReclamationFA', ['id' => $id]);
    }
    #[Route('/reclamation/modifierStatut/{id}', name: 'modifier_statut_reclamation')]
    public function modifierStatutReclamation($id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer la réclamation à modifier
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);

        // Vérifier si la réclamation existe
        if (!$reclamation) {
            throw $this->createNotFoundException('Reclamation not found');
        }

        // Modifier le statut de la réclamation en "Non traité"
        $reclamation->setStatusReclamation('non traité');

        // Enregistrer la réclamation modifiée dans la base de données
        $entityManager->flush();

        // Rediriger vers la même route de détail de réclamation avec le même ID
        return $this->redirectToRoute('detailReclamationFA', ['id' => $id]);
    }


    #[Route('/reclamation/statsReclamation', name: 'statsReclamation')]
    public function statsReclamation(ReclamationRepository $reclamationRepository): Response
{
    $reclamationStats = $reclamationRepository->countByStatus();
    $reclamationStatsDate = $reclamationRepository->countByDate();
    $reclamationStatsMonth = $reclamationRepository->countByMonth();

    return $this->render('reclamation/statsReclamation.html.twig', [
        'reclamationStats' => $reclamationStats,
        'reclamationStatsDate' => $reclamationStatsDate,
        'reclamationStatsMonth' => $reclamationStatsMonth,
    ]);
}
    #[Route('/reclamation/redirectMessagerie/{id}', name: 'redirectMessagerie')]
public function redirectMessagerie(int $id): RedirectResponse
{
    // Rediriger vers la route afficherMessagerie du contrôleur MessagerieController
    return $this->redirectToRoute('afficherMessagerie', ['id' => $id]);
}
}

