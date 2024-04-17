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
        $userId = 48; 
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
                    $image->move($this->getParameter('uploadsDirectory'), $fileName);
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
                $image->move($this->getParameter('uploadsDirectory'), $fileName);
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
public function detailEquipementFront($id, EquipementRepository $equipementRepository): Response
{
    // Récupérer l'équipement par son ID
    $equipement = $equipementRepository->find($id);

    if (!$equipement) {
        throw $this->createNotFoundException('Equipement not found');
    }

    // Passer l'équipement à la vue Twig pour affichage
    return $this->render('equipement/detailEquipementFront.html.twig', [
        'equipement' => $equipement,
    ]);
}
#[Route('/equipement/use/{id}', name: 'equipement_use')]
public function useEquipement($id, EquipementRepository $rep, EntityManagerInterface $entityManager): JsonResponse
{
    $equipement = $rep->find($id);

    if (!$equipement) {
        return new JsonResponse(['error' => 'Equipement not found'], Response::HTTP_NOT_FOUND);
    }

    // Vérifier si la quantité de l'équipement est supérieure à zéro
    if ($equipement->getQuantiteEq() <= 0) {
        return new JsonResponse(['error' => 'Cannot use equipment, quantity is zero'], Response::HTTP_BAD_REQUEST);
    }

    // Mettez en œuvre votre logique pour marquer l'équipement comme utilisé ici
    // Par exemple, décrémentez la quantité de l'équipement
    $equipement->setQuantiteEq($equipement->getQuantiteEq() - 1);

    // Persistez les modifications dans la base de données
    $entityManager->flush();

    return new JsonResponse(['message' => 'Equipement marked as used successfully']);
}
#[Route('/equipement/return/{id}', name: 'equipement_return')]
public function returnEquipement($id, EquipementRepository $rep, EntityManagerInterface $entityManager): JsonResponse
{
    $this->logger->info('Reached the returnEquipement function.'); 
    $equipement = $rep->find($id);

    if (!$equipement) {
        return new JsonResponse(['error' => 'Equipement not found'], Response::HTTP_NOT_FOUND);
    }
    
    // Définir la quantité initiale
    $quantiteInitiale = 0; // Vous pouvez initialiser cette valeur à ce que vous voulez

    // Récupérer la quantité initiale de l'équipement si la méthode getQuantiteEq() existe
    if (method_exists($equipement, 'getQuantiteEq')) {
        $quantiteInitiale = $equipement->getQuantiteEq();
    } 

    // Mettez en œuvre votre logique pour rendre l'équipement ici
    $equipement->setQuantiteEq($quantiteInitiale);

    // Persistez les modifications dans la base de données
    $entityManager->flush();

    return new JsonResponse(['message' => 'Equipement marked as returned successfully', 'quantiteInitiale' => $quantiteInitiale]);
}
#[Route('/equipement/showEquipementFront', name: 'equipement_show_front')]
public function showEquipementFront(Request $request, EquipementRepository $repository): Response
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
    ]);
}
} 
