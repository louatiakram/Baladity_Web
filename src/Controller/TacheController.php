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


class TacheController extends AbstractController
{
    #[Route('/tache', name: 'app_tache')]
    public function index(TacheRepository $r): Response
    {
        $xs = $r->findAll();
        return $this->render('tache/list.html.twig', ['l' => $xs,]);
    }

    #[Route('/tache/list', name: 'tache_list')]
    public function list(Request $request, TacheRepository $repository): Response
    {
        $query = $request->query->get('query');

        // If a search query is provided, filter tasks based on the title
        if ($query) {
            $tasks = $repository->findByTitre($query); // Replace with appropriate method
        } else {
            // If no search query is provided, fetch all tasks
            $tasks = $repository->findAll();
        }

        return $this->render('tache/list.html.twig', [
            'l' => $tasks,
            'query' => $query, // Pass the query to the template for displaying in the search bar
        ]);
    }

    #[Route('/tache/add', name: 'tache_add')]
    public function add(Request $req, ManagerRegistry $doctrine): Response
    {
        $userId = 50; // Assuming the user ID is 50
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $x= new tache();
        $x->setIdUser($user);

        $form = $this->createForm(TacheType::class, $x);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
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
        return $this->renderForm('tache/add.html.twig', ['f' => $form,]);
    }
    #[Route('/tache/detail/{i}', name: 'tache_detail')]
    public function detail($i, TacheRepository $rep): Response
    {
        $tache = $rep->find($i);
        if (!$tache) {
            throw $this->createNotFoundException('Task not found');
        }

        return $this->render('tache/detail.html.twig', ['tache' => $tache]);
    }
    #[Route('/tache/update/{i}', name: 'tache_update')]
    public function update($i, TacheRepository $rep, Request $req, ManagerRegistry $doctrine): Response
    {
        $x = $rep->find($i);
        $form = $this->createForm(TacheType::class, $x);
        $form->handleRequest($req);

        if ($form->isSubmitted()) {
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
    public function listfront(): Response
    {
        $taches = $this->getDoctrine()->getRepository(Tache::class)->findAll();

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
            return new JsonResponse(['error' => 'Tache not found'], Response::HTTP_NOT_FOUND);
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




}
