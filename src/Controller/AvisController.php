<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Avis;
use App\Form\AvisType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use DateTime;
use App\Entity\enduser;
use App\Entity\Equipement;
use App\Repository\AvisRepository;
use App\Repository\EquipementRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\Tools\Pagination\Paginator;

class AvisController extends AbstractController
{
    #[Route('/avis', name: 'app_avis')]
    public function index(): Response
    {
        return $this->render('avis/index.html.twig', [
            'controller_name' => 'AvisController',
        ]);
    }
    #[Route('/avis/ajouterAvisFront/{id}', name: 'ajouterAvisFront')]
    public function ajouterAvisFront($id, ManagerRegistry $doctrine, Request $req): Response
    {
        // Récupérer l'identifiant de l'utilisateur
             $userId = $req->getSession()->get('user_id');
        //get user
                $userRepository = $doctrine->getRepository(enduser::class);
                $users = $userRepository->findOneBy(['id_user' => $userId]);
    
        // Récupérer l'équipement depuis la base de données en utilisant son ID
        $equipement = $this->getDoctrine()->getRepository(Equipement::class)->find($id);
    
        // Vérifier si l'équipement existe
        if (!$equipement) {
            throw $this->createNotFoundException('Equipement not found');
        }
    
        // Créer une nouvelle instance de l'entité Avis
        $avis = new Avis();
    
        // Récupérer l'utilisateur à partir de son identifiant
        $user = $this->getDoctrine()->getRepository(Enduser::class)->find($userId);
    
        // Vérifier si l'utilisateur existe
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        // Définir l'utilisateur de l'avis
        $avis->setIdUser($user);
        
        // Définir l'équipement de l'avis
        $avis->setEquipement($equipement); // Utiliser la méthode setEquipement pour associer l'équipement à l'avis
    
        // Définir la date actuelle pour la propriété date_avis
        $avis->setDateAvis(new \DateTime());
    
        // Créer un formulaire pour l'avis
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($req);
    
        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
    
            // Obtenir le gestionnaire d'entités
            $em = $doctrine->getManager();
    
            // Persister l'objet avis dans la base de données
            $em->persist($avis);
            $em->flush();
    
            // Rediriger vers une page de succès ou afficher un message de succès
            return $this->redirectToRoute('avis_show_front', ['id' => $equipement->getIdEquipement()]);
        }
    
        // Dans le cas où le formulaire n'est pas soumis ou n'est pas valide,
        // Rendre le template avec le formulaire et les données associées
        return $this->render('avis/ajouterAvisFront.html.twig', [
            'form' => $form->createView(),
            'equipement' => $equipement,
            'avis' => $avis, // Passer la variable 'avis' à votre template Twig
            'user' => $users,
        ]);
    }
    
    #[Route('/avis/deleteAvis/{id}', name: 'avis_delete')]
public function deleteAvis($id, AvisRepository $rep, ManagerRegistry $doctrine): Response
{
    $avis = $rep->find($id);

    if (!$avis) {
        throw $this->createNotFoundException('Avis not found');
    }

    // Récupérer l'ID de l'équipement avant la suppression de l'avis
    $equipementId = $avis->getEquipement()->getIdEquipement();

    $em = $doctrine->getManager();
    $em->remove($avis);
    $em->flush();

    // Rediriger vers la page de liste des avis de l'équipement après la suppression réussie
    return $this->redirectToRoute('avis_show_front', ['id' => $equipementId]);
}
#[Route('/avis/deleteAvisResponsable/{id}', name: 'avis_delete_responsable')]
public function deleteAvisResponsable($id, AvisRepository $rep, ManagerRegistry $doctrine): Response
{
    $avis = $rep->find($id);

    if (!$avis) {
        throw $this->createNotFoundException('Avis not found');
    }

    // Récupérer l'ID de l'équipement avant la suppression de l'avis
    $equipementId = $avis->getEquipement()->getIdEquipement();

    $em = $doctrine->getManager();
    $em->remove($avis);
    $em->flush();

    // Rediriger vers la page de liste des avis de l'équipement après la suppression réussie
    return $this->redirectToRoute('avis_show_responsable', ['id' => $equipementId]);
}
#[Route('/avis/deleteAvisAdmin/{id}', name: 'avis_delete_admin')]
public function deleteAvisAdmin($id, AvisRepository $rep, ManagerRegistry $doctrine): Response
{
    $avis = $rep->find($id);

    if (!$avis) {
        throw $this->createNotFoundException('Avis not found');
    }

    // Récupérer l'ID de l'équipement avant la suppression de l'avis
    $equipementId = $avis->getEquipement()->getIdEquipement();

    $em = $doctrine->getManager();
    $em->remove($avis);
    $em->flush();

    // Rediriger vers la page de liste des avis de l'équipement après la suppression réussie
    return $this->redirectToRoute('avis_show', ['id' => $equipementId]);
}
    #[Route('/avis/modifierAvis/{id}', name: 'modifierAvis')]
    public function modifierAvis($id, ManagerRegistry $doctrine, Request $request): Response
    {
        $userId = $request->getSession()->get('user_id');
        //get user
                $userRepository = $doctrine->getRepository(enduser::class);
                $users = $userRepository->findOneBy(['id_user' => $userId]);
        $entityManager = $doctrine->getManager();
        $avis = $entityManager->getRepository(Avis::class)->find($id);
    
        if (!$avis) {
            throw $this->createNotFoundException('Avis not found');
        }
    
        // Create the form for modifying the avis
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle form submission
            $avis->setDateAvis(new DateTime());
            // Persist the modified avis object to the database
            $entityManager->flush();
    
            // Redirect to a success page or display a success message
            // For example:
            return $this->redirectToRoute('avis_show_front', ['id' => $avis->getEquipement()->getIdEquipement()]);
        }
        return $this->render('avis/modifierAvis.html.twig', [
            'form' => $form->createView(),
            'avis' => $avis,
            'equipement' => $avis->getEquipement(),
            'user' => $users,
        ]);
    }
    #[Route('/avis/showAvis/{id}', name: 'avis_show')]
    public function showAvis($id, Request $request, EquipementRepository $equipementRepository, AvisRepository $avisRepository): Response
    {
        // Récupérer l'équipement par son ID
        $equipement = $equipementRepository->find($id);
    
        if (!$equipement) {
            throw $this->createNotFoundException('Equipement not found');
        }
    
        $query = $request->query->get('query');
        $currentPage = $request->query->getInt('page', 1);
        $limit = 10; // Nombre d'avis par page
    
        // Récupérer les avis en fonction de l'équipement, de la recherche et de la pagination
        $queryBuilder = $avisRepository->createQueryBuilder('a')
            ->andWhere('a.equipement = :equipement')
            ->setParameter('equipement', $equipement)
            ->orderBy('a.date_avis', 'DESC'); // Tri par date de création, par exemple
    
        if ($query) {
            $queryBuilder->andWhere('a.titre LIKE :query')
                ->setParameter('query', '%'.$query.'%');
        }
    
        // Calculer l'offset
        $offset = ($currentPage - 1) * $limit;
    
        // Définir les limites de pagination
        $queryBuilder->setMaxResults($limit)
            ->setFirstResult($offset);
    
        // Exécuter la requête
        $paginator = new Paginator($queryBuilder->getQuery(), $fetchJoinCollection = true);
    
        // Calculer le nombre total de pages
        $totalPages = ceil(count($paginator) / $limit);
    
        return $this->render('avis/showAvis.html.twig', [
            'avis' => $paginator,
            'equipement' => $equipement,
            'query' => $query,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }
    #[Route('/avis/showAvisFront/{id}', name: 'avis_show_front')]
    
public function showAvisFront($id, Request $request, EquipementRepository $equipementRepository, AvisRepository $avisRepository, ManagerRegistry $doctrine): Response
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

    $query = $request->query->get('query');
    $currentPage = $request->query->getInt('page', 1);
    $limit = 10; // Nombre d'avis par page

    // Récupérer les avis en fonction de l'équipement, de la recherche et de la pagination
    $queryBuilder = $avisRepository->createQueryBuilder('a')
        ->andWhere('a.equipement = :equipement')
        ->setParameter('equipement', $equipement)
        ->orderBy('a.date_avis', 'DESC'); // Tri par date de création, par exemple

    if ($query) {
        $queryBuilder->andWhere('a.commentaireAvis LIKE :query')
            ->setParameter('query', '%'.$query.'%');
    }

    // Calculer l'offset
    $offset = ($currentPage - 1) * $limit;

    // Définir les limites de pagination
    $queryBuilder->setMaxResults($limit)
        ->setFirstResult($offset);

    // Exécuter la requête
    $paginator = new Paginator($queryBuilder->getQuery(), $fetchJoinCollection = true);

    // Calculer le nombre total de pages
    $totalPages = ceil(count($paginator) / $limit);

    return $this->render('avis/showAvisFront.html.twig', [
        'avis' => $paginator,
        'equipement' => $equipement,
        'id' => $id,
        'query' => $query,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'user' => $users,
    ]);
}

#[Route('/avis/showAvisResponsable/{id}', name: 'avis_show_responsable')]
    
public function showAvisResponsable($id, Request $request, EquipementRepository $equipementRepository, AvisRepository $avisRepository, ManagerRegistry $doctrine): Response
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

    $query = $request->query->get('query');
    $currentPage = $request->query->getInt('page', 1);
    $limit = 10; // Nombre d'avis par page

    // Récupérer les avis en fonction de l'équipement, de la recherche et de la pagination
    $queryBuilder = $avisRepository->createQueryBuilder('a')
        ->andWhere('a.equipement = :equipement')
        ->setParameter('equipement', $equipement)
        ->orderBy('a.date_avis', 'DESC'); // Tri par date de création, par exemple

    if ($query) {
        $queryBuilder->andWhere('a.commentaireAvis LIKE :query')
            ->setParameter('query', '%'.$query.'%');
    }

    // Calculer l'offset
    $offset = ($currentPage - 1) * $limit;

    // Définir les limites de pagination
    $queryBuilder->setMaxResults($limit)
        ->setFirstResult($offset);

    // Exécuter la requête
    $paginator = new Paginator($queryBuilder->getQuery(), $fetchJoinCollection = true);

    // Calculer le nombre total de pages
    $totalPages = ceil(count($paginator) / $limit);

    return $this->render('avis/showAvisResponsable.html.twig', [
        'avis' => $paginator,
        'equipement' => $equipement,
        'id' => $id,
        'query' => $query,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'user' => $users,
    ]);
}
}    
