<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Equipement;
use App\Form\EquipementType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use DateTime;
use App\Entity\enduser;
use App\Repository\EquipementRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Twilio\Rest\Client;

class EquipementController extends AbstractController
{
    #[Route('/equipement', name: 'app_equipement')]
    public function index(): Response
    {
        return $this->render('equipement/index.html.twig', [
            'controller_name' => 'EquipementController',
        ]);
    }
    #[Route('/equipement/ajouterEquipement', name: 'ajouterEquipement')]     
    public function ajouterEquipement(ManagerRegistry $doctrine, Request $req): Response
    {
        $equipement = new Equipement();
        $userId = $req->getSession()->get('user_id');
        //get user
                $userRepository = $doctrine->getRepository(enduser::class);
                $users = $userRepository->findOneBy(['id_user' => $userId]);
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);
    
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        $equipement->setIdUser($user);
        
        // Set the current date to the date_a property
        $equipement->setDateAjouteq(new DateTime());
    
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Set the image_a field
            $image = $form->get('image_eq')->getData();
            if ($image) {
                // Handle image upload and persist its filename to the database
                $fileName = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move($this->getParameter('uploads_directory'), $fileName);
                    $equipement->setImageEq($fileName);
                } catch (FileException $e) {
                    // Handle the exception if file upload fails
                    // For example, log the error or display a flash message
                }
            }
    
            // Get the entity manager
            $em = $doctrine->getManager();
    
            // Persist the equipement object to the database
            $em->persist($equipement);
            $em->flush();
    
            // Redirect to a success page or display a success message
            // For example:
            return $this->redirectToRoute('equipement_show');

        }
    
        return $this->render('equipement/ajouterEquipement.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/equipement/ajouterEquipementResponsable', name: 'ajouterEquipementResponsable')]     
    public function ajouterEquipementResponsable(ManagerRegistry $doctrine, Request $req): Response
    {
        $equipement = new Equipement();
       $userId = $req->getSession()->get('user_id');
    //get user
            $userRepository = $doctrine->getRepository(enduser::class);
            $users = $userRepository->findOneBy(['id_user' => $userId]);
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);
    
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        $equipement->setIdUser($user);
        
        // Set the current date to the date_a property
        $equipement->setDateAjouteq(new DateTime());
    
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Set the image_a field
            $image = $form->get('image_eq')->getData();
            if ($image) {
                // Handle image upload and persist its filename to the database
                $fileName = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move($this->getParameter('uploads_directory'), $fileName);
                    $equipement->setImageEq($fileName);
                } catch (FileException $e) {
                    // Handle the exception if file upload fails
                    // For example, log the error or display a flash message
                }
            }
    
            // Get the entity manager
            $em = $doctrine->getManager();
    
            // Persist the equipement object to the database
            $em->persist($equipement);
            $em->flush();
    
            // Redirect to a success page or display a success message
            // For example:
            return $this->redirectToRoute('equipement_show_responsable');

        }
    
        return $this->render('equipement/ajouterEquipementResponsable.html.twig', [
            'form' => $form->createView(),
            'user' => $users,
        ]);
    }
    #[Route('/equipement/deleteEquipement/{id}', name: 'equipement_delete')]
public function deleteEquipement($id, EquipementRepository $rep, ManagerRegistry $doctrine): Response
    {
        $equipement = $rep->find($id);
    
        if (!$equipement) {
            throw $this->createNotFoundException('Equipement not found');
        }
    
        $em = $doctrine->getManager();
        $em->remove($equipement);
        $em->flush();
    
        // Redirect to a success page or return a response as needed
        // For example:
        return $this->redirectToRoute('equipement_show');
    }
    #[Route('/equipement/deleteEquipementResponsable/{id}', name: 'equipement_delete_responsable')]
    public function deleteEquipementResponsable($id, EquipementRepository $rep, ManagerRegistry $doctrine): Response
        {
            $equipement = $rep->find($id);
        
            if (!$equipement) {
                throw $this->createNotFoundException('Equipement not found');
            }
        
            $em = $doctrine->getManager();
            $em->remove($equipement);
            $em->flush();
        
            // Redirect to a success page or return a response as needed
            // For example:
            return $this->redirectToRoute('equipement_show_responsable');
        }
    #[Route('/equipement/modifierEquipement/{id}', name: 'modifierEquipement')]
public function modifierEquipement($id, ManagerRegistry $doctrine, Request $request): Response
{
    $entityManager = $doctrine->getManager();
    $equipement = $entityManager->getRepository(Equipement::class)->find($id);

    if (!$equipement) {
        throw $this->createNotFoundException('Equipement not found');
    }

    // Create the form for modifying the equipement
    $form = $this->createForm(EquipementType::class, $equipement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle form submission
        $equipement->setDateAjouteq(new DateTime());
        // Set the image_eq field
        $image = $form->get('image_eq')->getData();
        if ($image) {
            // Handle image upload and persist its filename to the database
            $fileName = uniqid().'.'.$image->guessExtension();
            try {
                $image->move($this->getParameter('uploads_directory'), $fileName);
                $equipement->setImageEq($fileName);
            } catch (FileException $e) {
                // Handle the exception if file upload fails
                // For example, log the error or display a flash message
            }
        }

        // Persist the modified equipement object to the database
        $entityManager->flush();

        // Redirect to a success page or display a success message
        // For example:
        return $this->redirectToRoute('equipement_show');
    }

    return $this->render('equipement/modifierEquipement.html.twig', [
        'form' => $form->createView(),
        'equipement' => $equipement,
    ]);
}

#[Route('/equipement/modifierEquipementResponsable/{id}', name: 'modifierEquipementResponsable')]
public function modifierEquipementResponsable($id, ManagerRegistry $doctrine, Request $request): Response
{
    $userId = $request->getSession()->get('user_id');
    //get user
            $userRepository = $doctrine->getRepository(enduser::class);
            $users = $userRepository->findOneBy(['id_user' => $userId]);
    $entityManager = $doctrine->getManager();
    $equipement = $entityManager->getRepository(Equipement::class)->find($id);

    if (!$equipement) {
        throw $this->createNotFoundException('Equipement not found');
    }

    // Create the form for modifying the equipement
    $form = $this->createForm(EquipementType::class, $equipement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle form submission
        $equipement->setDateAjouteq(new DateTime());
        // Set the image_eq field
        $image = $form->get('image_eq')->getData();
        if ($image) {
            // Handle image upload and persist its filename to the database
            $fileName = uniqid().'.'.$image->guessExtension();
            try {
                $image->move($this->getParameter('uploads_directory'), $fileName);
                $equipement->setImageEq($fileName);
            } catch (FileException $e) {
                // Handle the exception if file upload fails
                // For example, log the error or display a flash message
            }
        }

        // Persist the modified equipement object to the database
        $entityManager->flush();

        // Redirect to a success page or display a success message
        // For example:
        return $this->redirectToRoute('equipement_show_responsable');
    }

    return $this->render('equipement/modifierEquipementResponsable.html.twig', [
        'form' => $form->createView(),
        'equipement' => $equipement,
        'user' => $users,
    ]);
}
#[Route('/equipement/showEquipement', name: 'equipement_show')]
public function showEquipement(Request $request, EquipementRepository $repository): Response
{
    $query = $request->query->get('query');
    $currentPage = $request->query->getInt('page', 1);
    $limit = 10; // Nombre d'équipements par page

    // Récupérer les équipements en fonction de la recherche et de la pagination
    if ($query) {
        $equipements = $repository->findByTitre($query, $limit, ($currentPage - 1) * $limit);
        $totalEquipements = count($equipements); // Mise à jour du nombre total d'équipements
    } else {
        $equipements = $repository->findAllPaginated($limit, ($currentPage - 1) * $limit);
        $totalEquipements = $repository->countAll(); // Mise à jour du nombre total d'équipements
    }

    // Calculer le nombre total de pages
    $totalPages = ceil($totalEquipements / $limit);

    return $this->render('equipement/showEquipement.html.twig', [
        'equipements' => $equipements,
        'query' => $query,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
    ]);
}
#[Route('/equipement/detail/{id}', name: 'equipement_detail')]
public function detailEquipement($id, EquipementRepository $equipementRepository): Response
{
    // Récupérer l'équipement par son ID
    $equipement = $equipementRepository->find($id);

    if (!$equipement) {
        throw $this->createNotFoundException('Equipement not found');
    }

    // Passer l'équipement à la vue Twig pour affichage
    return $this->render('equipement/detailEquipement.html.twig', [
        'equipement' => $equipement,
    ]);
}
#[Route('/equipement/detailFront/{id}', name: 'equipement_detail_front')]
public function detailEquipementFront($id, EquipementRepository $equipementRepository, ManagerRegistry $doctrine, Request $request): Response
{
    $userId = $request->getSession()->get('user_id');
    //get user
            $userRepository = $doctrine->getRepository(enduser::class);
            $users = $userRepository->findOneBy(['id_user' => $userId]);
    // Récupérer l'équipement par son ID
    $equipement = $equipementRepository->find($id);

    if (!$equipement) {
        throw $this->createNotFoundException('Equipement not found');
    }

    // Passer l'équipement à la vue Twig pour affichage
    return $this->render('equipement/detailEquipementFront.html.twig', [
        'equipement' => $equipement,
        'user' => $users,
    ]);
}
#[Route('/equipement/detailEquipementResponsable/{id}', name: 'equipement_detail_responsable')]
public function detailEquipementResponsable($id, EquipementRepository $equipementRepository, ManagerRegistry $doctrine, Request $request): Response
{
    $userId = $request->getSession()->get('user_id');
    //get user
            $userRepository = $doctrine->getRepository(enduser::class);
            $users = $userRepository->findOneBy(['id_user' => $userId]);
    // Récupérer l'équipement par son ID
    $equipement = $equipementRepository->find($id);

    if (!$equipement) {
        throw $this->createNotFoundException('Equipement not found');
    }

    // Passer l'équipement à la vue Twig pour affichage
    return $this->render('equipement/detailEquipementResponsable.html.twig', [
        'equipement' => $equipement,
        'user' => $users,
    ]);
}

#[Route('/equipement/showEquipementFront', name: 'equipement_show_front')]
public function showEquipementFront(Request $request, EquipementRepository $repository, ManagerRegistry $doctrine): Response
{
    $userId = $request->getSession()->get('user_id');
    //get user
            $userRepository = $doctrine->getRepository(enduser::class);
            $users = $userRepository->findOneBy(['id_user' => $userId]);    
    $query = $request->query->get('query');
    $category = $request->query->get('category');
    $currentPage = $request->query->getInt('page', 1);
    $limit = 10; // Nombre d'équipements par page
    $categories = $repository->findAllCategories(); // Utilisez $repository ici

    // Récupérer les équipements en fonction de la recherche et de la pagination
    $equipements = [];
    $totalEquipements = 0;
    if ($query || $category) {
        $equipements = $repository->findBySearchAndCategory($query, $category, $limit, ($currentPage - 1) * $limit);
        $totalEquipements = count($equipements); // Mise à jour du nombre total d'équipements
    } else {
        $equipements = $repository->findAllPaginated($limit, ($currentPage - 1) * $limit);
        $totalEquipements = $repository->countAll(); // Mise à jour du nombre total d'équipements
    }
    
    // Calculer et transmettre la quantité initiale pour chaque équipement
    foreach ($equipements as $equipement) {
        $equipement->quantiteInitiale = $equipement->getQuantiteEq();
    }

    // Calculer le nombre total de pages
    $totalPages = ceil($totalEquipements / $limit);

    return $this->render('equipement/showEquipementFront.html.twig', [
        'equipements' => $equipements,
        'query' => $query,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'categories' => $categories,
        'user' => $users,
    ]);
}
#[Route('/equipement/utiliser/{id}', name: 'equipement_utiliser')]
public function utiliserEquipement($id, EquipementRepository $equipementRepository, EntityManagerInterface $entityManager): JsonResponse
{
    // Récupérer l'équipement par son ID
    $equipement = $equipementRepository->find($id);

    if (!$equipement) {
        return new JsonResponse(['error' => 'Equipement introuvable'], Response::HTTP_NOT_FOUND);
    }

    // Vérifier si la quantité d'équipement est supérieure à zéro
    if ($equipement->getQuantiteEq() > 0) {
        // Décrémenter la quantité d'équipement disponible
        $nouvelleQuantite = $equipement->getQuantiteEq() - 1;
        $equipement->setQuantiteEq($nouvelleQuantite);
        $entityManager->flush();

        if ($nouvelleQuantite == 0) {
            // Configuration du client Twilio avec les paramètres
            $accountSid = $this->getParameter('twilio_account_sid');
            $authToken = $this->getParameter('twilio_auth_token');
            $twilioNumber = '+19284400733'; // Remplacez par votre numéro Twilio

            // Initialisation du client Twilio
            $twilioClient = new Client($accountSid, $authToken);

            // Envoi du SMS à l'administrateur pour l'informer que le stock de l'équipement est épuisé
            $adminPhoneNumber = '+21655907840'; // Remplacez par le numéro de téléphone de l'administrateur
            $messageBody = 'Le stock de l\'équipement '.$equipement->getNomEq().' (Référence: '.$equipement->getReferenceEq().') est épuisé.';
            $twilioClient->messages->create(
                $adminPhoneNumber,
                ['from' => $twilioNumber, 'body' => $messageBody]
            );
        }

        return new JsonResponse(['success' => true]);
    } else {
        return new JsonResponse(['error' => 'Stock déjà épuisé pour l\'équipement'], Response::HTTP_BAD_REQUEST);
    }
}
#[Route('/equipement/rendre/{id}', name: 'equipement_rendre')]
public function rendreEquipement($id, Request $request, EquipementRepository $repository): JsonResponse
{
    // Récupérer l'équipement par son ID
    $equipement = $repository->find($id);

    if (!$equipement) {
        return new JsonResponse(['error' => 'Equipement introuvable'], Response::HTTP_NOT_FOUND);
    }

    // Récupérer la quantité utilisée envoyée depuis la requête AJAX
    $quantiteUtilisee = $request->request->get('quantite_utilisee');

    // Vérifier si la quantité utilisée est valide
    if (!is_numeric($quantiteUtilisee) || $quantiteUtilisee <= 0) {
        return new JsonResponse(['error' => 'Quantité utilisée invalide'], Response::HTTP_BAD_REQUEST);
    }

    // Effectuer les vérifications supplémentaires ici, par exemple, si la quantité utilisée ne dépasse pas la quantité disponible, etc.

    // Mettre à jour l'équipement avec la nouvelle quantité (exemple)
    $nouvelleQuantite = $equipement->getQuantiteEq() + $quantiteUtilisee;
    $equipement->setQuantiteEq($nouvelleQuantite);
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->flush();

    return new JsonResponse(['success' => true]);
}
#[Route('/equipement/statsEquipements', name: 'stats_equipements')]
public function statsEquipements(EquipementRepository $equipementRepository): Response
{
    try {
        // Récupérer les statistiques par jour et par mois
        $equipementsParJour = $equipementRepository->countEquipementsAddedByDay();
        $equipementsParMois = $equipementRepository->countEquipementsAddedByMonth();
        $countEquipementsFixe = $equipementRepository->countEquipementsByCategory('Fixe');
        $countEquipementsMobile = $equipementRepository->countEquipementsByCategory('Mobile');

        return $this->render('equipement/statsEquipements.html.twig', [
            'equipementsParJour' => $equipementsParJour,
            'equipementsParMois' => $equipementsParMois,
            'countEquipementsFixe' => $countEquipementsFixe,
            'countEquipementsMobile' => $countEquipementsMobile,
        ]);
    } catch (\Exception $e) {
        // En cas d'erreur, retourner une réponse JSON avec le message d'erreur
        return new JsonResponse(['error' => $e->getMessage()], 400);
    }
}
#[Route('/equipement/showEquipementResponsable', name: 'equipement_show_responsable')]
public function showEquipementResponsable(Request $request, EquipementRepository $repository, ManagerRegistry $doctrine): Response
{
    $userId = $request->getSession()->get('user_id');
    //get user
            $userRepository = $doctrine->getRepository(enduser::class);
            $users = $userRepository->findOneBy(['id_user' => $userId]);
    $query = $request->query->get('query');
    $currentPage = $request->query->getInt('page', 1);
    $limit = 10; // Nombre d'équipements par page

    // Récupérer les équipements en fonction de la recherche et de la pagination
    if ($query) {
        $equipements = $repository->findByTitre($query, $limit, ($currentPage - 1) * $limit);
        $totalEquipements = count($equipements); // Mise à jour du nombre total d'équipements
    } else {
        $equipements = $repository->findAllPaginated($limit, ($currentPage - 1) * $limit);
        $totalEquipements = $repository->countAll(); // Mise à jour du nombre total d'équipements
    }

    // Calculer et transmettre la quantité initiale pour chaque équipement
    foreach ($equipements as $equipement) {
        $equipement->quantiteInitiale = $equipement->getQuantiteEq();
    }

    // Calculer le nombre total de pages
    $totalPages = ceil($totalEquipements / $limit);

    return $this->render('equipement/showEquipementResponsable.html.twig', [
        'equipements' => $equipements,
        'query' => $query,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'user' => $users,
    ]);
}
}
