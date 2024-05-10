<?php

namespace App\Controller;

use DateTime;
use App\Entity\enduser;
use App\Entity\reclamation;
use App\Form\ReclamationType;
use Symfony\Component\Mime\Email;
use App\Form\ReclamationAdminType;
use App\Form\ReclamationModifierType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReclamationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        return $this->render('reclamation/typeReclamation.html.twig');
    }
    #[Route('/reclamation/typeReclamationF', name: 'typeReclamationF')]
    public function typeReclamationF(ManagerRegistry $doctrine, Request $request): Response
    {
        $userId = $request->getSession()->get('user_id');
        //get user
                $userRepository = $doctrine->getRepository(enduser::class);
                $users = $userRepository->findOneBy(['id_user' => $userId]);
        return $this->render('reclamation/typeReclamationF.html.twig', [
            'user' => $users,
        ]);
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
public function ajouterReclamationF(Request $request, $cas,MailerInterface $mailer,ManagerRegistry $doctrine): Response
{
     // Récupérer l'utilisateur à partir du formulaire
     $userId = $request->getSession()->get('user_id');
     //get user
             $userRepository = $doctrine->getRepository(enduser::class);
             $users = $userRepository->findOneBy(['id_user' => $userId]);
     
             $user = $this->getDoctrine()->getRepository(EndUser::class)->find($userId);
    
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
        $imageFileName = null; // This will hold the name of the uploaded image

        if ($image) {
            // Gérer le téléchargement de l'image et enregistrer son nom de fichier dans la base de données
            $fileName = uniqid().'.'.$image->guessExtension();
            try {
                $image->move($this->getParameter('uploads_directory'), $fileName);
                $reclamation->setImageReclamation($fileName);
                $imageFileName = $fileName;
            } catch (FileException $e) {
                // Gérer l'exception si le téléchargement du fichier échoue
                // Par exemple, journaliser l'erreur ou afficher un message flash
            }
        }
         // Upload image to imgbb if an image was provided
         if ($imageFileName) {
            $client = HttpClient::create();
            $response = $client->request('POST', 'https://api.imgbb.com/1/upload', [
                'body' => [
                    'image' => base64_encode(file_get_contents($this->getParameter('uploads_directory') . '/' . $imageFileName)),
                    'key' => '9891cf19363a960cf78207d0934b3f79', // Replace with your actual API key
                ],
            ]);
            // Vérifier la réponse HTTP
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Erreur lors du téléchargement de l\'image sur imgbb.');
            }
            $content = $response->toArray();
        
            // You can now use the $content array to access the uploaded image URL and other details
            $imageUrl = $content['data']['url'];
            // You might want to save this URL to your entity or use it as needed in your application
        }
        
        // Get the entity manager
        $em = $this->getDoctrine()->getManager();

        // Persist the reclamation object to the database
        $em->persist($reclamation);
        $em->flush();

        // Ajout du message flash
        $this->addFlash('success', 'La réclamation a été ajoutée avec succès.');
        // Create a new email
        $email = (new Email())
        ->from('zayaneyassine6@gmail.com') 
        ->to($user->getEmailUser()) 
        //->cc('exemple@mail.com') 
        //->bcc('exemple@mail.com')
        //->replyTo('test42@gmail.com')
            ->priority(Email::PRIORITY_HIGH) 
            ->subject('Reclamation')
        // If you want use text mail only
            ->text(' La réclamation a été envoyée avec succès. ');
            $mailer->send($email);


        // Redirection vers la page d'affichage des réclamations
       return $this->redirectToRoute('afficherReclamationF');
    }

    return $this->render('reclamation/ajouterReclamationF.html.twig', [
        'form' => $form->createView(),
        'user' => $users,
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
public function afficherReclamationF(Request $request, ReclamationRepository $repository, PaginatorInterface $paginator,ManagerRegistry $doctrine): Response
{
    $userId = $request->getSession()->get('user_id');
//get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $users = $userRepository->findOneBy(['id_user' => $userId]);


    // Récupérer les réclamations de l'utilisateur actuel
    $reclamations = $repository->findReclamationsByUserId($userId);

    return $this->render('reclamation/afficherReclamationF.html.twig', [
        'reclamations' => $reclamations,
        'user' => $users,
    ]);
}
#[Route('/reclamation/afficherReclamationFA', name: 'afficherReclamationFA')]
public function afficherReclamationFA(Request $request, ReclamationRepository $repository, PaginatorInterface $paginator,ManagerRegistry $doctrine): Response
{
    // Récupérer toutes les réclamations
    $userId = $request->getSession()->get('user_id');
    //get user
            $userRepository = $doctrine->getRepository(enduser::class);
            $users = $userRepository->findOneBy(['id_user' => $userId]);
    $reclamations = $repository->findAll();

    return $this->render('reclamation/afficherReclamationA.html.twig', [
        'reclamations' => $reclamations,
        'user' => $users,
    ]);
}


#[Route('/reclamation/filtrerParDate', name: 'filtrerParDate')]
public function filtrerParDate(Request $request, ReclamationRepository $repository, SessionInterface $session, ManagerRegistry $doctrine): Response
{
    $userId = $request->getSession()->get('user_id');
    //get user
            $userRepository = $doctrine->getRepository(enduser::class);
            $users = $userRepository->findOneBy(['id_user' => $userId]);
    $sortingState = $session->get('sorting_state', 'normal');
    
    if ($sortingState === 'normal') {
        $userId = $request->getSession()->get('user_id');
        //get user
                $userRepository = $doctrine->getRepository(enduser::class);
                $users = $userRepository->findOneBy(['id_user' => $userId]);
        $reclamations = $repository->findReclamationsByDate($userId);
        $session->set('sorting_state', 'sorted');
    } else {
        $userId = $request->getSession()->get('user_id');
        //get user
                $userRepository = $doctrine->getRepository(enduser::class);
                $users = $userRepository->findOneBy(['id_user' => $userId]);
        $reclamations = $repository->findReclamationsByUserId($userId);
        $session->set('sorting_state', 'normal');    }

    return $this->render('reclamation/afficherReclamationF.html.twig', [
        'reclamations' => $reclamations,
        'sorting_state' => $sortingState,
        'user' => $users,
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
        $userId = $request->getSession()->get('user_id');
        //get user
                $userRepository = $doctrine->getRepository(enduser::class);
                $users = $userRepository->findOneBy(['id_user' => $userId]);
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
            'user' => $users,
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
    public function detailReclamationF($id, ReclamationRepository $rep,ManagerRegistry $doctrine,Request $request): Response
    {
        $userId = $request->getSession()->get('user_id');
        //get user
                $userRepository = $doctrine->getRepository(enduser::class);
                $users = $userRepository->findOneBy(['id_user' => $userId]);
        $reclamation=$rep->find($id);
        return $this->render('reclamation/detailReclamationF.html.twig', [
            'reclamation' => $reclamation,
            'user' => $users,
        ]);

    }

    #[Route('/reclamation/detailReclamationFA/{id}', name: 'detailReclamationFA')]
    public function detailReclamationFA($id, ReclamationRepository $rep,ManagerRegistry $doctrine,Request $request): Response
    {
        $userId = $request->getSession()->get('user_id');
        //get user
                $userRepository = $doctrine->getRepository(enduser::class);
                $users = $userRepository->findOneBy(['id_user' => $userId]);
        $reclamation=$rep->find($id);
        return $this->render('reclamation/detailReclamationFA.html.twig', [
            'reclamation' => $reclamation,
            'user' => $users,
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
#[Route('/reclamation/statsReclamationF', name: 'statsReclamationF')]
public function statsReclamationF(ReclamationRepository $reclamationRepository, ManagerRegistry $doctrine, Request $request): Response
{
    $userId = $request->getSession()->get('user_id');
    // Récupérer l'utilisateur
    $userRepository = $doctrine->getRepository(Enduser::class);
    $user = $userRepository->findOneBy(['id_user' => $userId]);

    // Récupérer les statistiques de réclamation
    $reclamationStats = $reclamationRepository->countByStatus();
    $reclamationStatsDate = $reclamationRepository->countByDate();
    $reclamationStatsMonth = $reclamationRepository->countByMonth();

    return $this->render('reclamation/statsReclamationF.html.twig', [
        'reclamationStats' => $reclamationStats,
        'reclamationStatsDate' => $reclamationStatsDate,
        'reclamationStatsMonth' => $reclamationStatsMonth,
        'user' => $user,
    ]);
}
    #[Route('/reclamation/redirectMessagerie/{id}', name: 'redirectMessagerie')]
public function redirectMessagerie(int $id): RedirectResponse
{
    // Rediriger vers la route afficherMessagerie du contrôleur MessagerieController
    return $this->redirectToRoute('afficherMessagerie', ['id' => $id]);
}

#[Route('/reclamation/send-email/{id}', name: 'send_reclamation_email')]
public function sendReclamationEmail(MailerInterface $mailer, Request $request, ManagerRegistry $doctrine, $id): Response
{
    $userId = $request->getSession()->get('user_id');
    //get user
            $userRepository = $doctrine->getRepository(enduser::class);
            $users = $userRepository->findOneBy(['id_user' => $userId]);
    // Find the Reclamation object
    $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);

    // Check if the Reclamation object is found
    if (!$reclamation) {
        throw $this->createNotFoundException('Reclamation not found');
    }
    if ($request->isMethod('POST')) {
        // Récupérer le sujet et le message du formulaire
        $subject = $request->get('subject');
        $message = $request->get('message');
        

        // Valider le formulaire
    
        // Find the Reclamation object
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);

        // Get the recipient's email address
        $recipientEmail = $reclamation->getIdUser()->getEmailUser();


        // Si l'e-mail est envoyé avec succès, redirigez l'utilisateur ou affichez un message de succès
        // Par exemple, rediriger vers une page de confirmation
        return $this->redirectToRoute('send_mail_reclamation', [
            'emailSaisie' => $recipientEmail,
            'subject' => $subject,
            'message' => $message,
        ]);

    }
    // If the email was sent successfully, render the success response
    return $this->render('reclamation/traitementReclamation.html.twig', [
        'reclamation' => $reclamation,
        'user' => $users,
    ]);
}

}

