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
use DateTime;
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
        $actualite = new Actualite();
        $userId = 48; // Assuming the user ID is 1
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);
    
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        $actualite->setIdUser($user);
        
        // Set the current date to the date_a property
        $actualite->setDateA(new DateTime());
    
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Set the image_a field
            $image = $form->get('image_a')->getData();
            if ($image) {
                // Handle image upload and persist its filename to the database
                $fileName = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move($this->getParameter('uploadsDirectory'), $fileName);
                    $actualite->setImageA($fileName);
                } catch (FileException $e) {
                    // Handle the exception if file upload fails
                    // For example, log the error or display a flash message
                }
            }
    
            // Get the entity manager
            $em = $doctrine->getManager();
    
            // Persist the actualite object to the database
            $em->persist($actualite);
            $em->flush();
    
            // Redirect to a success page or display a success message
            // For example:
            return $this->redirectToRoute('app_main');
        }
    
        return $this->render('actualite/ajouterA.html.twig', [
            'form' => $form->createView()
        ]);
    }
    

    } 