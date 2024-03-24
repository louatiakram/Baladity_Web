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
    #[Route('/equipement/deleteEquipement/{i}', name: 'equipement_delete')]
    public function deleteEquipement($i, EquipementRepository $rep, ManagerRegistry $doctrine): Response
    {
        $equipement = $rep->find($i);
    
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
    $form = $this->createForm(Equipement::class, $equipement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle form submission
        $equipement->setDateAjouteq(new DateTime());
        // Set the image_a field
        $image = $form->get('image_eq')->getData();
        if ($image) {
            // Handle image upload and persist its filename to the database
            $fileName = uniqid().'.'.$image->guessExtension();
            try {
                $image->move($this->getParameter('uploadsDirectory'), $fileName);
                $equipement->setImageA($fileName);
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

// Fetch the current page number from the query parameters
$currentPage = $request->query->getInt('page', 1);

// Assuming you have logic to calculate the total number of pages, let's say it's stored in $totalPages
$totalPages = 5; // Replace this with your actual calculation

// If a search query is provided, filter tasks based on the title
if ($query) {
    $tasks = $repository->findByTitre($query); // Replace with appropriate method
} else {
    // If no search query is provided, fetch all tasks
    $tasks = $repository->findAll();
}

return $this->render('equipement/showEquipement.html.twig', [
    'l' => $tasks,
    'query' => $query,
    'currentPage' => $currentPage, // Pass the currentPage variable to the Twig template
    'totalPages' => $totalPages, // Pass the totalPages variable to the Twig template
]);
}
 } 
