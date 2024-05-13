<?php

namespace App\Controller;

use App\Entity\actualite;
use App\Form\ActualiteType;
use App\Entity\enduser;
use Knp\Component\Pager\PaginatorInterface;

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
use App\Repository\ActualiteRepository;
use Symfony\Component\String\Slugger\AsciiSlugger;
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
        $userId = $req->getSession()->get('user_id');
        //get user
                $userRepository = $doctrine->getRepository(enduser::class);
                $users = $userRepository->findOneBy(['id_user' => $userId]);
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
              $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $slugger = new AsciiSlugger();
            $safeFilename = $slugger->slug($originalFilename)->lower()->toString();
            $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
            if ($image) {
                // Handle image upload and persist its filename to the database
                $fileName = uniqid() . '.' . $image->guessExtension();
                try {
                    $image->move($this->getParameter('uploads_directory'),  $newFilename, $fileName);
                    
                    $actualite->setImageA($fileName);
 // Copy the file to the other directory
 $targetDirectoryJava = 'C:/Users/amine/Desktop/PiDev/3A5_DevMasters/src/main/resources/assets';
 $targetPathJava = $targetDirectoryJava . '/' . $newFilename;
 copy($this->getParameter('uploads_directory').'/'.$newFilename, $targetPathJava);

                } catch (FileException $e) {
                    // Handle the exception if file upload fails
                    // For example, log the error or display a flash message
                }
                $actualite->setImageA($newFilename);
            } else {
                // Add an error to the image_a field if no image is selected
                $form->get('image_a')->addError(new FormError('Vous devez sélectionner une image.'));
            }
    
            // Check if there are any errors after adding the custom error
            if ($form->isValid()) {
                // Make sure image_a is not null before persisting
                if (!$actualite->getImageA()) {
                    // Add an error to the form
                    $form->addError(new FormError('Une image est requise.'));
                } else {
                    // Get the entity manager
                    $em = $doctrine->getManager();
    
                    // Persist the actualite object to the database
                    $em->persist($actualite);
                    $em->flush();
    
                    // Redirect to a success page or display a success message
                    return $this->redirectToRoute('actualite_show');
                }
            }
        }
    
        return $this->render('actualite/ajouterA.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/actualite/deleteA/{i}', name: 'actualite_delete')]
    public function deleteA($i, ActualiteRepository $rep, ManagerRegistry $doctrine): Response
    {
        $actualite = $rep->find($i);
    
        if (!$actualite) {
            throw $this->createNotFoundException('Actualite not found');
        }
    
        $em = $doctrine->getManager();
        $em->remove($actualite);
        $em->flush();
    
        // Redirect to a success page or return a response as needed
        // For example:
        return $this->redirectToRoute('actualite_show');
    }
    #[Route('/actualite/showA', name: 'actualite_show')]
    public function showA(Request $request, ActualiteRepository $repository, PaginatorInterface $paginator): Response
    {
        $query = $request->query->get('query');
    
        // Fetch the current page number from the query parameters
        $currentPage = $request->query->getInt('page', 1);
    
        // Number of items per page
        $itemsPerPage = 3;
    
        // Create a query builder for finding the records
        if ($query) {
            // If a search query is provided, filter based on the title
            $queryBuilder = $repository->createQueryBuilder('a')
                ->where('a.titre LIKE :query')
                ->setParameter('query', '%' . $query . '%');
        } else {
            // If no search query is provided, fetch all records
            $queryBuilder = $repository->createQueryBuilder('a');
        }
    
        // Use the Paginator service to paginate the results
        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(), // Doctrine Query object
            $currentPage, // Current page number
            $itemsPerPage // Number of items per page
        );
    
        return $this->render('actualite/showA.html.twig', [
            'pagination' => $pagination, // Pass the paginated results to the template
            'query' => $query,
        ]);
    }

 #[Route('/actualite/modifierA/{id}', name: 'modifierA')]

    public function modifierA($id, ManagerRegistry $doctrine, Request $request): Response

{
    $entityManager = $doctrine->getManager();
    $actualite = $entityManager->getRepository(Actualite::class)->find($id);

    if (!$actualite) {
        throw $this->createNotFoundException('Actualite not found');
    }

    // Create the form for modifying the actualite
    $form = $this->createForm(ActualiteType::class, $actualite);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle form submission
        $actualite->setDateA(new DateTime());
        // Set the image_a field
        $image = $form->get('image_a')->getData();
        if ($image) {
            // Handle image upload and persist its filename to the database
            $fileName = uniqid().'.'.$image->guessExtension();
            try {
                $image->move($this->getParameter('uploads_directory'), $fileName);
                $actualite->setImageA($fileName);
            } catch (FileException $e) {
                // Handle the exception if file upload fails
                // For example, log the error or display a flash message
            }
        }

        // Persist the modified actualite object to the database
        $entityManager->flush();

        // Redirect to a success page or display a success message
        // For example:
        return $this->redirectToRoute('actualite_show');
    }

    return $this->render('actualite/modifierA.html.twig', [
        'form' => $form->createView(),
        'actualite' => $actualite,
    ]);
}
#[Route('/actualiteCitoyen', name: 'app_actualite')]
public function index1(ActualiteRepository $repository): Response
{
    $actualites = $repository->findAll(); // Fetch all actualités from the repository

    return $this->render('actualite/showACitoyen.html.twig', [
        'actualites' => $actualites,
        
    ]);
}
#[Route('/actualiteResponsable', name: 'ajouterA2')]
public function ajouterA2(ManagerRegistry $doctrine, Request $req): Response
{
    $actualite = new Actualite();
    $userId = $req->getSession()->get('user_id');
//get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $users = $userRepository->findOneBy(['id_user' => $userId]);
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
       
        $image = $form->get('image_a')->getData();
        if ($image) {
            // Handle image upload and persist its filename to the database
            $fileName = uniqid().'.'.$image->guessExtension();
            try {
                $image->move($this->getParameter('uploads_directory'), $fileName);
                $actualite->setImageA($fileName);
            } catch (FileException $e) {
                // Handle the exception if file upload fails
                // For example, log the error or display a flash message
            }
        } else {
            // Add an error to the image_a field if no image is selected
            $form->get('image_a')->addError(new FormError('Vous devez sélectionner une image.'));
        }
        
        // Get the entity manager
        $em = $doctrine->getManager();

        // Persist the actualite object to the database
        $em->persist($actualite);
        $em->flush();

        // Redirect to a success page or display a success message
        // For example:
        return $this->redirectToRoute('app_actualiteshowResponsable');
    }

    return $this->render('actualite/ajouterAResponsable.html.twig', [
        'form' => $form->createView()
    ]);
}
#[Route('/showAResponsable', name: 'app_actualiteshowResponsable')]
public function index3(ActualiteRepository $repository): Response
{
    $actualites = $repository->findAll(); // Fetch all actualités from the repository

    return $this->render('actualite/ShowAResponsable.html.twig', [
        'actualites' => $actualites,
        
    ]);
}

#[Route('/ModifierResponsable/{id}', name: 'modifierA2')]

public function modifierA2($id, ManagerRegistry $doctrine, Request $request): Response

{
    $entityManager = $doctrine->getManager();
    $actualite = $entityManager->getRepository(Actualite::class)->find($id);

    if (!$actualite) {
        throw $this->createNotFoundException('Actualite not found');
    }

    // Create the form for modifying the actualite
    $form = $this->createForm(ActualiteType::class, $actualite);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle form submission
        $actualite->setDateA(new DateTime());
        // Set the image_a field
        $image = $form->get('image_a')->getData();
        if ($image) {
            // Handle image upload and persist its filename to the database
            $fileName = uniqid().'.'.$image->guessExtension();
            try {
                $image->move($this->getParameter('uploads_directory'), $fileName);
                $actualite->setImageA($fileName);
            } catch (FileException $e) {
                // Handle the exception if file upload fails
                // For example, log the error or display a flash message
            }
        }

        // Persist the modified actualite object to the database
        $entityManager->flush();

        // Redirect to a success page or display a success message
        // For example:
        return $this->redirectToRoute('app_actualiteshowResponsable');
    }

    return $this->render('actualite/modifierAResponsable.html.twig', [
        'form' => $form->createView(),
        'actualite' => $actualite,
    ]);
}
#[Route('/actualite/details/{id}', name: 'actualite_details')]
public function showDetails($id, ActualiteRepository $actualiteRepository, PubliciteRepository $publiciteRepository): Response
{
    // Récupérer l'actualité spécifique
    $actualite = $actualiteRepository->find($id);

    if (!$actualite) {
        throw $this->createNotFoundException('Actualite not found');
    }

    // Récupérer les publicités associées à cette actualité
    $publicites = $publiciteRepository->findBy(['id_a' => $actualite]);

    return $this->render('actualite/details.html.twig', [
        'actualite' => $actualite,
        'publicites' => $publicites,
    ]);
}

#[Route('/actualite/search', name: 'search_actualites', methods: ['GET', 'POST'])]
public function search(Request $request, ActualiteRepository $repository): Response
{
    $query = $request->query->get('query');
    $currentPage = $request->query->getInt('page', 1);

    if ($query) {
        $actualites = $repository->findByTitre($query);
    } else {
        $actualites = $repository->findAll();
    }

    // If the request is AJAX, return JSON data
    if ($request->isXmlHttpRequest()) {
        $actualitesArray = array_map(fn($a) => [
            'titre' => $a->getTitreA(),
            'description' => $a->getDescriptionA(),
            'image' => $a->getImageA(),
            'date' => $a->getDateA()->format('Y-m-d'),
            'id' => $a->getIdA(),
        ], $actualites);

        return new JsonResponse($actualitesArray);
    }

    return $this->render('actualite/showAResponsable.html.twig', [
        'actualites' => $actualites,
        'query' => $query,
        'currentPage' => $currentPage,
        'totalPages' => 10, // This would be calculated from data
    ]);
}

#[Route('/publicite/stats', name: 'stats')]
public function statsA(ActualiteRepository $actualiteRepository): Response
{
    try {
        // Récupérer les statistiques par date
        $statsByDate = $actualiteRepository->countByDate();
        $statsByMonth = $actualiteRepository->countByMonth();

        return $this->render('publicite/stats.html.twig', [
            'statsByDate' => $statsByDate,
            'statsByMonth' => $statsByMonth,
        ]);
    } catch (\Exception $e) {
        // En cas d'erreur, retourner une réponse JSON avec le message d'erreur
        return new JsonResponse(['error' => $e->getMessage()], 400);
    }
}

 }