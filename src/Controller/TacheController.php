<?php

namespace App\Controller;

use App\Entity\tache;
use App\Form\TacheType;
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
use Symfony\Component\Form\FormError;   



class TacheController extends AbstractController
{
    #[Route('/tache', name: 'tache_list')]
    public function list(Request $request, TacheRepository $repository): Response
    {
        
        // Get the current page number from the request query parameters, default to 1
        $page = $request->query->getInt('page', 1);

         // Calculate the offset based on the current page number and limit per page
        $limit = 4;
        $offset = ($page - 1) * $limit;

        // If no search query is provided, fetch all tasks with pagination
        $tasks = $repository->findBy([], ['date_FT' => 'ASC'], $limit, $offset);

// Count total number of tasks for pagination
$totalTasks = $repository->count([]);

// Calculate total number of pages
$totalPages = ceil($totalTasks / $limit);

return $this->render('tache/list.html.twig', [
    'l' => $tasks,
    'currentPage' => $page, // Pass the current page number
    'totalPages' => $totalPages, // Pass the total number of pages for pagination
    ]);
}

    #[Route('/tache/add', name: 'tache_add')]
    public function add(Request $req, ManagerRegistry $doctrine): Response
    {
        $userId = 50; // Assuming the user ID is 50
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User Existe Pas');
        }

        $x= new tache();
        $x->setIdUser($user);

        $form = $this->createForm(TacheType::class, $x);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            
        $em = $doctrine->getManager();
        // Check if a task with the same titre_T and nom_Cat already exists
        $existingTask = $em->getRepository(tache::class)->findOneBy([
            'titre_T' => $x->getTitreT(),
            'nom_Cat' => $x->getNomCat(),
        ]);

        if ($existingTask) {
            $form->addError(new FormError('Une tâche avec le même titre et la même catégorie existe déjà !'));
        } else {
            
            // Handle file upload
            /** @var UploadedFile|null $pieceJointe */
            $pieceJointe = $form->get('pieceJointe_T')->getData();
            if ($pieceJointe) {
                $originalFilename = pathinfo($pieceJointe->getClientOriginalName(), PATHINFO_FILENAME);
                // Move the file to the uploads directory
                try {
                    $uploadedFile = $pieceJointe->move(
                        $this->getParameter('uploadsDirectory'), // Use the parameter defined in services.yaml
                        $originalFilename.'.'.$pieceJointe->guessExtension()
                    );
                    $x->setPieceJointeT($uploadedFile->getFilename());
                } catch (FileException $e) {
                }
            }

            // Get the selected etat_T value from the form
            $selectedEtatT = $form->get('etat_T')->getData();

            // Set the etat_T property of the tache entity
            $x->setEtatT($selectedEtatT);

            $em = $doctrine->getManager();
            $em->persist($x);
            $em->flush();
            return $this->redirectToRoute('tache_list');
        }

    }
        return $this->renderForm('tache/add.html.twig', ['f' => $form,]);
    }
    
    #[Route('/tache/detail/{i}', name: 'tache_detail')]
    public function detail($i, TacheRepository $rep): Response
    {
        $tache = $rep->find($i);
        if (!$tache) {
            throw $this->createNotFoundException('Tache Existe Pas');
        }

        return $this->render('tache/detail.html.twig', ['tache' => $tache]);
    }
    
    #[Route('/tache/update/{i}', name: 'tache_update')]
    public function update($i, TacheRepository $rep, Request $req, ManagerRegistry $doctrine): Response
    {
        $x = $rep->find($i);
        $form = $this->createForm(TacheType::class, $x);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Handle file upload
            /** @var UploadedFile|null $pieceJointe */
            $pieceJointe = $form->get('pieceJointe_T')->getData();
            if ($pieceJointe) {
                $originalFilename = pathinfo($pieceJointe->getClientOriginalName(), PATHINFO_FILENAME);
                // Move the file to the uploads directory
                try {
                    $uploadedFile = $pieceJointe->move(
                        $this->getParameter('uploadsDirectory'), // Use the parameter defined in services.yaml
                        $originalFilename.'.'.$pieceJointe->guessExtension()
                    );
                    $x->setPieceJointeT($uploadedFile->getFilename());
                } catch (FileException $e) {
                }
            }
            // Get the selected etat_T value from the form
            $selectedEtatT = $form->get('etat_T')->getData();

            // Set the etat_T property of the tache entity
            $x->setEtatT($selectedEtatT);

            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('tache_list');
        }

        return $this->renderForm('tache/add.html.twig', ['f' => $form,]);
    }
    
    #[Route('/tache/delete/{i}', name: 'tache_delete')]
    public function delete($i, TacheRepository $rep, ManagerRegistry $doctrine): Response
    {
        $xs = $rep->find($i);
        $em = $doctrine->getManager();
        $em->remove($xs);
        $em->flush();
        return $this->redirectToRoute('tache_list');
    }

    #[Route('/tache/listfront', name: 'tache_listfront')]
    public function listfront(Request $request, TacheRepository $repository): Response
    {
         // If no search query is provided, fetch all tasks with pagination
         $taches = $repository->findBy([], ['date_FT' => 'ASC']);
        //$taches = $this->getDoctrine()->getRepository(Tache::class)->findAll();

        return $this->render('tache/listfront.html.twig', [
            'taches' => $taches,
        ]);
    }

    #[Route('/update-tache-state/{tacheId}/{newState}', name: 'update_tache_state')]
    public function updateTacheState(Request $request, int $tacheId, string $newState): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tache = $entityManager->getRepository(Tache::class)->find($tacheId);

        if (!$tache) {
            return new JsonResponse(['error' => 'Tache Existe Pas'], Response::HTTP_NOT_FOUND);
        }

        // Update etat_T attribute of the tache entity
        $tache->setEtatT($newState);

        try {
            $entityManager->flush(); // Save changes to the database
            return new JsonResponse(['message' => 'Tache state updated successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to update tache state'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/tache/piechart', name: 'tache_piechart')]
    public function pieChart(TacheRepository $tacheRepository): Response
    {
        // Get the count of tasks done by each user
        $usersTasksCount = $tacheRepository->getUsersTasksCount();

        // Extract user names and task counts from the result
        $data = [];
        foreach ($usersTasksCount as $result) {
            $userName = $result['user_name'];
            $taskCount = $result['task_count'];
            $data[] = ['user_name' => $userName, 'task_count' => $taskCount];
        }

        return $this->render('tache/piechart.html.twig', [
            'data' => $data // Pass data to twig template
        ]);
    }
}