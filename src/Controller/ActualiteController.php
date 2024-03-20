<?php

namespace App\Controller;

use App\Entity\actualite;
use App\Form\ActualiteType;
use App\Entity\enduser;
use App\Repository\TacheRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormError;

class ActualiteController extends AbstractController
{
    #[Route('/actualite', name: 'app_actualite')]
    public function index(): Response
    {
        return $this->render('actualite/index.html.twig', [
            'controller_name' => 'ActualiteController',
        ]);
    }
    #[Route('/actualite/ajouterA', name: 'ajouterA')]     
    public function ajouterA(ManagerRegistry $doctrine, Request $req): Response
    {
        $actualites = new Actualite();
        
        // Create the form based on the ActualiteType form class
        $form = $this->createForm(ActualiteType::class, $actualites);
    
        // Handle form submission
        $form->handleRequest($req);
    
        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('image_a')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Move the file to the uploads directory
                try {
                    $uploadedFile = $imageFile->move(
                        $this->getParameter('images_directory'), // Use the parameter defined in services.yaml
                        $originalFilename.'.'.$imageFile->guessExtension()
                    );
                    $x->setImageA($uploadedFile->getFilename());
                } catch (FileException $e) {
                    // Handle the exception if necessary
                }
            }
            
            
            // Get the entity manager
            $em = $doctrine->getManager();  
            
            // Persist the actualites object to the database
            $em->persist($actualites);
            $em->flush();
            
            // Create a new instance of the form to reset it
            $actualites = new Actualite();
            $form = $this->createForm(ActualiteType::class, $actualites);
    
            // Add a success message if needed
            // $this->addFlash('success', 'Actualite added successfully.');
    
            // Render the template with the new form instance
            return $this->render('actualite/ajouterA.html.twig', [
                'form' => $form->createView() // Pass the form view to the template
            ]);
        }
        
        // Render the template with the form
        return $this->render('actualite/ajouterA.html.twig', [
            'form' => $form->createView() // Pass the form view to the template
        ]);
    }
    

    } 