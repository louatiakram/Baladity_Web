<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormError;
use DateTime;
use App\Repository\PubliciteRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\publicite;
use App\Entity\enduser;
use App\Form\PubliciteType;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use TCPDF;
use App\Repository\ActualiteRepository;

class PubliciteController extends AbstractController
{
    #[Route('/publicite', name: 'app_publicite')]
    public function index(): Response
    {
        return $this->render('publicite/index.html.twig', [
            'controller_name' => 'PubliciteController',
        ]);
    }
    
    #[Route('/publicite/showPub', name: 'publicite_show')]
    public function showPub(Request $request, PubliciteRepository $repository): Response
    {
        // Fetch the search query from the request
        $query = $request->query->get('query');
    
        // Fetch the current page number from the query parameters
        $currentPage = $request->query->getInt('page', 1);
    
        // Fetch the total number of pages (replace with your actual logic)
        $totalPages = 10; // Replace this with your actual calculation
    
        // If a search query is provided, filter publicités based on the title
        if ($query) {
            $publicites = $repository->findByTitre($query); // Replace with appropriate method
        } else {
            // If no search query is provided, fetch all publicités
            $publicites = $repository->findAll();
        }
    
        return $this->render('publicite/showPub.html.twig', [
            'publicites' => $publicites, // Corrected variable name
            'query' => $query,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }
    #[Route('/publicite/deletePub/{i}', name: 'deletePub')]
    public function deletePub($i, PubliciteRepository $rep, ManagerRegistry $doctrine): Response
    {
        $publicite = $rep->find($i);
    
        if (!$publicite) {
            throw $this->createNotFoundException('publicite not found');
        }
    
        $em = $doctrine->getManager();
        $em->remove($publicite);
        $em->flush();
    
        // Redirect to a success page or return a response as needed
        // For example:
        return $this->redirectToRoute('publicite_show');
    }
   

    #[Route('/publicite/ajouterPub', name: 'ajouterPub')]     
    public function ajouterPub(ManagerRegistry $doctrine, Request $req): Response
    {
        $publicite = new publicite();
        $userId = $req->getSession()->get('user_id');
//get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $users = $userRepository->findOneBy(['id_user' => $userId]);
    
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);
    
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        $publicite->setIdUser($user);
        
        $form = $this->createForm(PubliciteType::class, $publicite);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image_pub')->getData();
            if ($image) {
                $fileName = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move($this->getParameter('uploads_directory'), $fileName);
                    $publicite->setImagePub($fileName);
                } catch (FileException $e) {
                    // Handle the exception if file upload fails
                    // For example, log the error or display a flash message
                }
            } else {
                // Add an error to the image_pub field if no image is selected
                $form->get('image_pub')->addError(new FormError('Vous devez sélectionner une image.'));
            }
    
            // Render the form with errors if image_pub is not valid
            if ($form->isSubmitted() && !$form->isValid()) {
                return $this->render('publicite/ajouterPub.html.twig', [
                    'form' => $form->createView()
                ]);
            }
    
            // Get the entity manager
            $em = $doctrine->getManager();
    
            // Persist and flush the entity
            $em->persist($publicite);
            $em->flush();
    
            // Redirect to the appropriate route
            return $this->redirectToRoute('payment');
        }
    
        return $this->render('publicite/ajouterPub.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    #[Route('/publicite/modifierPub/{id}', name: 'modifierPub')]

    public function modifierPub($id, ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $publicite = $entityManager->getRepository(Publicite::class)->find($id);
    
        if (!$publicite) {
            throw $this->createNotFoundException('Publicité not found');
        }
    
        $form = $this->createForm(PubliciteType::class, $publicite);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image_pub')->getData();
            
            if ($image) {
                // Gérer le téléchargement de la nouvelle image
                $fileName = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move($this->getParameter('uploads_directory'), $fileName);
                    
                    // Supprimer l'ancienne image si elle existe
                    $oldImage = $publicite->getImagePub();
                    if ($oldImage) {
                        unlink($this->getParameter('uploads_directory') . '/' . $oldImage);
                    }
                    
                    // Mettre à jour le nom du fichier de l'image dans l'entité
                    $publicite->setImagePub($fileName);
                } catch (FileException $e) {
                    // Gérer l'erreur de téléchargement de fichier
                }
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_publicite');
        }
    
        return $this->render('publicite/modifierPub.html.twig', [
            'form' => $form->createView(),
            'publicite' => $publicite,
        ]);
    }
    
    #[Route('/showPubCitoyen', name: 'app_publicite')]
public function index1(PubliciteRepository $repository): Response
{
    $publicites = $repository->findAll(); 

    return $this->render('publicite/showPubCitoyen.html.twig', [
        'publicites' => $publicites,
        
    ]);
}
#[Route('/showPubResponsable', name: 'app_publiciteResponsable')]
public function index2(PubliciteRepository $repository): Response
{
    $publicites = $repository->findAll(); // Fetch all actualités from the repository

    return $this->render('publicite/showPubResponsable.html.twig', [
        'publicites' => $publicites,
        
    ]);
}

#[Route('/payment', name: 'payment')]
public function payment(Request $request): Response
{
    // Récupérer la durée de l'offre et le montant correspondant depuis la requête ou la base de données
    $offre = strtolower(trim($request->request->get('offre_pub')));  // Validation de l'offre

    $montant = 0; // Initialiser le montant

    // Définir le montant en fonction de la durée de l'offre
    switch ($offre) {
        case '3 mois':
            $montant = 1470; // 50 DT en centimes (par exemple, 50,00 DT)
            break;
        case '6 mois':
            $montant = 2647; // 90 DT en centimes
            break;
        case '9 mois':
            $montant = 3823; // 130 DT en centimes
            break;
        // Ajoutez d'autres cas selon vos besoins
    }

    // Configurer Stripe avec votre clé secrète
    Stripe::setApiKey('sk_test_51OpeMeI3VcdValufdQQI5nr0PLI1jmQ9YCCa6Xu4ozS5Qv9IBoaTSvqMtzZXaZf0edfdRkNVVLixMKfo8CtYx3PW00MLcbGNSd');

    try {
        // Créer un PaymentIntent avec les détails du paiement
        $paymentIntent = PaymentIntent::create([
            'amount' => $montant,
            'currency' => 'usd',
        ]);

        // Si le paiement réussit, afficher un message de succès
        $paymentStatus = 'Paiement réussi. ID du paiement : ' . $paymentIntent->id;
        $this->addFlash('success', 'Le paiement effectué avec success.');
    } catch (\Exception $e) {
       
        // Si une erreur survient lors du traitement du paiement, afficher le message d'erreur
        $paymentStatus = 'Le paiement a échoué. Erreur : ' . $e->getMessage();
    }
   // $this->addFlash('success', 'Le paiement effectué avec success.');
    // Rendre le template Twig avec le statut du paiement
    return $this->render('publicite/Payment.html.twig', [
        'paymentStatus' => $paymentStatus,
    ]);
}


#[Route('/generate-pdf', name: 'generate_pdf')]
public function generatePdf(Request $request): Response
{
    // Récupérer les valeurs du formulaire
    $nom = $request->request->get('nom');
    $prenom = $request->request->get('prenom');
    $numeroCarte = $request->request->get('numero_carte');
    $offre = $request->request->get('offre_pub');

    // Récupérer le fichier de l'image téléversée
    /** @var UploadedFile $logoFile */
    $logoFile = $request->files->get('logo');

    // Vérifier si un fichier a été téléversé
    if ($logoFile) {
        // Définir le répertoire de destination pour enregistrer l'image téléversée
        $logoImagePath = $this->getParameter('kernel.project_dir') . '/public/front-office/img/LOGOL.png';

        $logoFileName = md5(uniqid()) . '.' . $logoFile->guessExtension();

        // Déplacer le fichier téléversé vers le répertoire de destination
        try {
            $logoFile->move($uploads_directory, $logoFileName);
        } catch (FileException $e) {
            // Gérer l'erreur de déplacement du fichier
            // ...
        }

        // Construire le chemin complet de l'image téléversée
        $logoImagePath = '/uploads/' . $logoFileName;
    } else {
        // Utiliser une image par défaut si aucune image n'a été téléversée
        $logoImagePath = 'public\front-office\img\LOGOL.png';
    }

    // Créer une nouvelle instance de TCPDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    // Définir les informations du document
    $pdf->SetCreator('Votre Nom');
    $pdf->SetAuthor('Votre Nom');
    $pdf->SetTitle('Confirmation de Paiement');
    $pdf->SetSubject('Confirmation de Paiement');

    // Ajouter une page
    $pdf->AddPage();

    // Ajouter le logo
    $pdf->Image($logoImagePath, 10, 10, 50, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

    // Ajouter le titre
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->Cell(0, 10, 'Vérification de Paiement', 0, 1, 'C');

    // Ajouter les détails du paiement
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Nom: ' . $nom, 0, 1);
    $pdf->Cell(0, 10, 'Prénom: ' . $prenom, 0, 1);
    $pdf->Cell(0, 10, 'Numéro de carte: ' . $numeroCarte, 0, 1);
    $pdf->Cell(0, 10, 'Offre sélectionnée: ' . $offre, 0, 1);

    // Ajouter un message de confirmation
    $pdf->Ln(10); // Saut de ligne
    $pdf->Cell(0, 10, "Paiement reçu avec succès.\nLa vérification de paiement est confirmée.", 0, 1);

    // Générer le contenu PDF
    $pdfContent = $pdf->Output('offre.pdf', 'S');

    // Créer une réponse avec le contenu PDF
    $response = new Response($pdfContent);

    // Ajouter les en-têtes pour télécharger le fichier
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', 'attachment; filename="offre.pdf"');

    return $response;
}
#[Route('/publicite/recherche', name: 'publicite_recherche')]
public function recherche(Request $request, PubliciteRepository $repository): Response
{
    
    $titre = $request->query->get('titre');
    
    $queryBuilder = $repository->createQueryBuilder('p');

    if ($titre) {
        $queryBuilder->andWhere('p.titre_pub LIKE :titre')
                     ->setParameter('titre', '%' . $titre . '%');
    }

    $publicites = $queryBuilder->getQuery()->getResult();

    return $this->render('publicite/showPub.html.twig', [
        'publicites' => $publicites,
    ]);
}

#[Route('/publicite/sorted', name: 'publicite_sorted')]
public function sortedPublicites(Request $request, PubliciteRepository $repository): Response
{
    $sortBy = $request->query->get('sortBy', 'titre_pub'); // Default sort field
    $sortOrder = strtoupper($request->query->get('sortOrder', 'ASC')); // Default sort order

    // Add debugging information to check parameter values
    dump("Sort by: $sortBy, Order: $sortOrder");

    $publicites = $repository->findSortedPublicites($sortBy, $sortOrder);

    return $this->render('publicite/showPub.html.twig', [
        'publicites' => $publicites,
        'sortBy' => $sortBy,
        'sortOrder' => $sortOrder,
    ]);
}

#[Route('/publicite/stats', name: 'stats')]
public function statsPublicite(
    PubliciteRepository $publiciteRepository,
    ActualiteRepository $actualiteRepository // Add the repository to fetch date-related data
): Response {
    // Fetch existing statistics for offers
    $statsPublicite = $publiciteRepository->countByOffer();

    // Fetch additional statistics by date
    $statsByDate = $actualiteRepository->countByDate();

    // Fetch additional statistics by month (if needed)
    $statsByMonth = $actualiteRepository->countByMonth();

    // Render the template with all the statistics
    return $this->render('publicite/stats.html.twig', [
        'statsPublicite' => $statsPublicite,
        'statsA' => $statsByDate, // This is the variable expected by your Twig template
        'statsByMonth' => $statsByMonth, // If you need month-based statistics
    ]);
}

}
