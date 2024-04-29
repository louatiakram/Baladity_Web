<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\evenement;
use App\Entity\enduser;
use App\Repository\EvenementRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface; 
use App\Form\EvenementType;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\DomCrawler\Crawler;

class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'app_evenement')]
    public function index(): Response
    {
        return $this->render('evenement/index.html.twig', [
            'controller_name' => 'EvenementController',
        ]);
    }

    #[Route('/evenement/list', name: 'evenement_list')]
    public function list(Request $request, EvenementRepository $repository, PaginatorInterface $paginator): Response
    {
        $query = $request->query->get('query');
        $orderBy = $request->query->get('orderBy', 'default_value');
        // Fetch all events
        $queryBuilder = $repository->createQueryBuilder('e');
        if ($query) {
            // Use your repository method to search events by name
            $queryBuilder->where('e.nom_E LIKE :query')
                         ->setParameter('query', '%'.$query.'%');
        }
        if ($orderBy === 'nom') {
            $queryBuilder->orderBy('e.nom_E');
        } elseif ($orderBy === 'categorie') {
            $queryBuilder->orderBy('e.categorie');
        } else {
            $queryBuilder->orderBy('e.date_DHE');
        }
        $evenements = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1), // Current page number, default is 1
            5 // Number of items per page
        );
        return $this->render('evenement/list.html.twig', [
            'evenements'=> $evenements,
            'query' => $query, // Pass the query to the template for displaying in the search bar
            'orderBy' => $orderBy,
        ]);
    }
    
    #[Route('/evenement/listFront', name: 'evenement_listFront')]
public function listFront(Request $request, EvenementRepository $repository, PaginatorInterface $paginator): Response
{
    $query = $request->query->get('query');

    // Fetch all events
    $queryBuilder = $repository->createQueryBuilder('e');

    // If a search query is provided, filter events based on the name
    if ($query) {
        $queryBuilder->where('e.nomE LIKE :query')
                     ->setParameter('query', '%'.$query.'%');
    }

    $evenements = $paginator->paginate(
        $queryBuilder->getQuery(),
        $request->query->getInt('page', 1), // Current page number, default is 1
        6 // Number of items per page
    );

    return $this->render('evenement/listFront.html.twig', [
        'evenements' => $evenements,
        'query' => $query, // Pass the query to the template for displaying in the search bar
    ]);
}
#[Route('/evenement/listCitoyen', name: 'evenement_listCitoyen')]
public function listCitoyen(Request $request, EvenementRepository $repository, PaginatorInterface $paginator): Response
{
    $query = $request->query->get('query');

    // Fetch all events
    $queryBuilder = $repository->createQueryBuilder('e');

    // If a search query is provided, filter events based on the name
    if ($query) {
        $queryBuilder->where('e.nomE LIKE :query')
                     ->setParameter('query', '%'.$query.'%');
    }

    $evenements = $paginator->paginate(
        $queryBuilder->getQuery(),
        $request->query->getInt('page', 1), // Current page number, default is 1
        6 // Number of items per page
    );

    return $this->render('evenement/listCitoyen.html.twig', [
        'evenements' => $evenements,
        'query' => $query, // Pass the query to the template for displaying in the search bar
    ]);
}

    #[Route('/evenement/ajouter', name: 'ajouter_evenement')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
{
    // Créer une nouvelle instance d'événement
    $evenement = new Evenement();
    
    // Définir l'identifiant de l'utilisateur statique (48 dans ce cas)
    $evenement->setIdUser(48);

    // Gérer la soumission du formulaire
    $form = $this->createForm(EvenementType::class, $evenement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gérer le téléchargement de fichier
        $imageFile = $form->get('imageEvent')->getData();
        if ($imageFile) {
            $fileName = uniqid().'.'.$imageFile->guessExtension();
            try {
                $imageFile->move($this->getParameter('uploadsDirectory'), $fileName);
                $evenement->setImageEvent($fileName);
            } catch (FileException $e) {
                $this->addFlash('error', 'Failed to upload image.');
                return $this->redirectToRoute('ajouter_evenement');
            }
        }

        // Persister l'objet événement dans la base de données
        $entityManager->persist($evenement);
        $entityManager->flush();
        // Définir le message de succès dans la session
        $session->getFlashBag()->add('success', 'L\'événement a été ajouté avec succès.');

        // Rediriger vers une page de réussite ou une route
        return $this->redirectToRoute('evenement_list');
    }

    // Rendre la vue du formulaire
    return $this->render('evenement/ajouter.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/evenement/ajouterFront', name: 'ajouterFront_evenement')]
    public function ajouterFront(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
{
    // Créer une nouvelle instance d'événement
    $evenement = new Evenement();
    
    // Définir l'identifiant de l'utilisateur statique (48 dans ce cas)
    $evenement->setIdUser(48);

    // Gérer la soumission du formulaire
    $form = $this->createForm(EvenementType::class, $evenement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gérer le téléchargement de fichier
        $imageFile = $form->get('imageEvent')->getData();
        if ($imageFile) {
            $fileName = uniqid().'.'.$imageFile->guessExtension();
            try {
                $imageFile->move($this->getParameter('uploadsDirectory'), $fileName);
                $evenement->setImageEvent($fileName);
            } catch (FileException $e) {
                $this->addFlash('error', 'Failed to upload image.');
                return $this->redirectToRoute('ajouterFront_evenement');
            }
        }

        // Persister l'objet événement dans la base de données
        $entityManager->persist($evenement);
        $entityManager->flush();
        // Définir le message de succès dans la session
        $session->getFlashBag()->add('success', 'L\'événement a été ajouté avec succès.');

        // Rediriger vers une page de réussite ou une route
        return $this->redirectToRoute('evenement_listFront');
    }

    // Rendre la vue du formulaire
    return $this->render('evenement/ajouterFront.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/evenement/supprimer/{id}', name: 'supprimer_evenement')]
    public function supprimerEvenement($id, EvenementRepository $repository, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $evenement = $repository->find($id);

        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé avec l\'id : ' . $id);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($evenement);
        $entityManager->flush();

        $session->getFlashBag()->add('success', 'L\'événement a été supprimé avec succès.');

        return $this->redirectToRoute('evenement_list');
    }

    #[Route('/evenement/supprimerFront/{id}', name: 'supprimerFront_evenement')]
    public function supprimerEvenementFront($id, EvenementRepository $repository, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $evenement = $repository->find($id);

        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé avec l\'id : ' . $id);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($evenement);
        $entityManager->flush();

        $session->getFlashBag()->add('success', 'L\'événement a été supprimé avec succès.');

        return $this->redirectToRoute('evenement_listFront');
    }

#[Route('/evenement/modifier/{id}', name: 'modifier_evenement')]
public function update($id, EvenementRepository $rep, Request $req, ManagerRegistry $doctrine): Response
    {
        $x = $rep->find($id);
        $form = $this->createForm(EvenementType::class, $x);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

            // Handle file upload
            /** @var UploadedFile|null $pieceJointe */
            $image = $form->get('imageEvent')->getData();
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // Move the file to the uploads directory
                try {
                    $uploadedFile = $image->move(
                        $this->getParameter('uploadsDirectory'), // Use the parameter defined in services.yaml
                        $originalFilename . '.' . $image->guessExtension()
                    );
                    $x->setImageEvent($uploadedFile->getFilename());
                } catch (FileException $e) {}
            }
            // Get the selected etat_T value from the form
            $selectedCategorieE = $form->get('categorie_E')->getData();

            // Set the etat_T property of the tache entity
            $x->setCategorieE($selectedCategorieE);

            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('evenement_list');
        }
        return $this->renderForm('evenement/modifier.html.twig', ['form' => $form]);
    }

    #[Route('/evenement/modifierFront/{id}', name: 'modifierFront_evenement')]
public function updateFront($id, EvenementRepository $rep, Request $req, ManagerRegistry $doctrine): Response
    {
        $x = $rep->find($id);
        $form = $this->createForm(EvenementType::class, $x);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

            // Handle file upload
            /** @var UploadedFile|null $pieceJointe */
            $image = $form->get('imageEvent')->getData();
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // Move the file to the uploads directory
                try {
                    $uploadedFile = $image->move(
                        $this->getParameter('uploadsDirectory'), // Use the parameter defined in services.yaml
                        $originalFilename . '.' . $image->guessExtension()
                    );
                    $x->setImageEvent($uploadedFile->getFilename());
                } catch (FileException $e) {}
            }
            // Get the selected etat_T value from the form
            $selectedCategorieE = $form->get('categorie_E')->getData();

            // Set the etat_T property of the tache entity
            $x->setCategorieE($selectedCategorieE);

            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('evenement_listFront');
        }
        return $this->renderForm('evenement/modifierFront.html.twig', ['form' => $form]);
    }

    
    #[Route('/evenement/details/{id}', name: 'details_evenement')]
public function detailsEvenement($id, EntityManagerInterface $entityManager): Response
{
    $evenement = $entityManager->getRepository(Evenement::class)->find($id);

    if (!$evenement) {
        throw $this->createNotFoundException('Événement non trouvé avec l\'id : '.$id);
    }

    return $this->render('evenement/details.html.twig', [
        'evenement' => $evenement,
    ]);
}
#[Route('/evenement/detailsFront/{id}', name: 'details_evenementFront')]
public function detailsEvenementFront($id, EntityManagerInterface $entityManager): Response
{
    $evenement = $entityManager->getRepository(Evenement::class)->find($id);

    if (!$evenement) {
        throw $this->createNotFoundException('Événement non trouvé avec l\'id : '.$id);
    }

    return $this->render('evenement/detailsFront.html.twig', [
        'evenement' => $evenement,
    ]);
}

#[Route('/evenement/detailsCitoyen/{id}', name: 'details_evenementCitoyen')]
public function detailsEvenementCitoyen($id, EntityManagerInterface $entityManager): Response
{
    $evenement = $entityManager->getRepository(Evenement::class)->find($id);

    if (!$evenement) {
        throw $this->createNotFoundException('Événement non trouvé avec l\'id : '.$id);
    }

    return $this->render('evenement/detailsCitoyen.html.twig', [
        'evenement' => $evenement,
    ]);
}

public function __construct(Security $security)
{
    $this->security = $security;
}

#[Route('/evenement/join/{id}', name: 'join_evenement')]
public function joinEvenement($id, EvenementRepository $repository, EntityManagerInterface $entityManager, SessionInterface $session, Security $security): Response
{
    // Static user ID for testing
    $userId = 60; // Replace with the actual user ID

    // Get the event by ID
    $evenement = $repository->find($id);

    // Check if the event exists
    if (!$evenement) {
        throw $this->createNotFoundException('Événement non trouvé avec l\'id : ' . $id);
    }

    // Check if the event's start date has already passed
    $currentDate = new \DateTime();
    if ($evenement->getDateDHE() <= $currentDate) {
        $session->getFlashBag()->add('error', 'La date de début de l\'événement est déjà passée. Vous ne pouvez pas rejoindre cet événement.');
        return $this->redirectToRoute('evenement_listCitoyen');
    }

    // Get the authenticated user
    $user = $security->getUser();

    // Check if the user is authenticated
    if (!$user) {
        // Handle unauthenticated user
        // Redirect to login page or handle the case accordingly
    }

    // Check if the user has already joined the event (using session or cache)
    if ($evenement->getAttendees()->contains($user)) {
        $session->getFlashBag()->add('error', 'Vous avez déjà rejoint cet événement.');
        return $this->redirectToRoute('details_evenementFront', ['id' => $id]);
    }

    // Check if the capacity is greater than zero
    if ($evenement->getCapaciteE() <= 0) {
        $session->getFlashBag()->add('error', 'La capacité de l\'événement est atteinte. Vous ne pouvez pas rejoindre cet événement.');
        return $this->redirectToRoute('evenement_listCitoyen');
    }

    // Find the user by ID
    $enduser = $entityManager->getRepository(enduser::class)->find($userId);

    // Check if the user exists
    if (!$enduser) {
        throw $this->createNotFoundException('Utilisateur non trouvé avec l\'id : ' . $userId);
    }

    // Add the user as an attendee to the event
    $evenement->addAttendee($enduser);
    $entityManager->flush();
     // Decrement the capacity
     $evenement->setCapaciteE($evenement->getCapaciteE() - 1);
     $entityManager->flush();
    // Add success message
    $session->getFlashBag()->add('success', 'Vous avez rejoint l\'événement avec succès.');

    // Redirect to the event details page
    return $this->redirectToRoute('details_evenementCitoyen', ['id' => $id]);
}

#[Route('/evenement/stats', name: 'stats_evenement')]
    public function statsEvenement(EvenementRepository $evenementRep): Response
{
    $evenementStats = $evenementRep->countByCategorie();
    $evenementStatsDate = $evenementRep->countByDateDebut();
    $evenementStatsMonth = $evenementRep->countByMonth();

    return $this->render('evenement/stats.html.twig', [
        'evenementStats' => $evenementStats,
        'evenementStatsDate' => $evenementStatsDate,
        'evenementStatsMonth' => $evenementStatsMonth,
    ]);
}
#[Route('/evenement/generate-pdf/{id}', name: 'generate_pdf')]
public function generatePdfForEvenement($id, EntityManagerInterface $entityManager): Response
{
    // Find the event by ID
    $evenement = $entityManager->getRepository(Evenement::class)->find($id);

    if (!$evenement) {
        throw $this->createNotFoundException('Événement non trouvé avec l\'id : '.$id);
    }

    // Render the Twig template with event details
    $html = $this->renderView('evenement/pdf.html.twig', [
        'evenement' => $evenement,
    ]);

    // Configure dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);

    // Instantiate dompdf
    $dompdf = new Dompdf($options);

    // Load HTML content
    $dompdf->loadHtml($html);

    // Set paper size and orientation (optional)
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF
    $pdfContent = $dompdf->output();

    // Create a Response with the PDF content
    $response = new Response($pdfContent);

    // Set the content type header
    $response->headers->set('Content-Type', 'application/pdf');

    // Set the filename header
    $response->headers->set('Content-Disposition', 'attachment; filename="details_evenement.pdf"');

    return $response;
}
}