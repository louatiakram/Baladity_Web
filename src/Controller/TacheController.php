<?php

namespace App\Controller;

use App\Entity\CommentaireTache;
use App\Entity\enduser;
use App\Entity\tache;
use App\Form\CommentaireTacheType;
use App\Form\TacheType;
use App\Repository\TacheRepository;
use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Knp\Component\Pager\PaginatorInterface;
use League\Csv\Reader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;


class TacheController extends AbstractController
{

    #[Route('/tache', name: 'tache_list')]
    public function list(Request $request, TacheRepository $repository, PaginatorInterface $paginator, SessionInterface $session): Response
    {
        $defaultOrderBy = 'date_FT';
        $orderBy = $request->query->get('orderBy', 'date_FT'); // Default ordering by date_FT
        $queryBuilder = $repository->createQueryBuilder('t')->orderBy('t.' . $defaultOrderBy, 'ASC');

        // Set order by based on user input
        switch ($orderBy) {
            case 'titre':
                $queryBuilder->orderBy('t.titre_T', 'ASC');
                break;
            case 'etat':
                $queryBuilder->orderBy('t.etat_T', 'ASC');
                break;
            case 'date_FT':
            default:
                $queryBuilder->orderBy('t.date_FT', 'ASC');
                break;
        }

        // Set order direction
        $orderDirection = 'ASC';

        // Get start and end dates from the query parameters if provided
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        // If start and end dates are provided, apply date filtering
        if ($startDate && $endDate) {
            $queryBuilder
                ->andWhere('t.date_FT >= :startDate')
                ->andWhere('t.date_FT <= :endDate')
                ->setParameter('startDate', new \DateTime($startDate))
                ->setParameter('endDate', new \DateTime($endDate));
        }

        $query = $queryBuilder->getQuery();

        // Paginate the query
        $tasks = $paginator->paginate(
            $query, // Doctrine Query object
            $request->query->getInt('page', 1), // Page number
            3// Limit per page
        );

        // Calculate task counts for different "Etat" (status)
        $tasksDoneCount = $repository->countByEtat('DONE');
        $tasksDoingCount = $repository->countByEtat('DOING');
        $tasksToDoCount = $repository->countByEtat('TODO');

        $successMessage = $session->getFlashBag()->get('success');

        return $this->render('tache/list.html.twig', [
            'tasks' => $tasks,
            'successMessage' => $successMessage ? $successMessage[0] : null,
            'orderBy' => $orderBy,
            'tasksDoneCount' => $tasksDoneCount,
            'tasksDoingCount' => $tasksDoingCount,
            'tasksToDoCount' => $tasksToDoCount,
            'startDate' => $startDate, // Pass the start date to pre-fill the date picker
            'endDate' => $endDate, // Pass the end date to pre-fill the date picker
        ]);
    }

    #[Route('/tachedir', name: 'tache_listdir')]
    public function tachedir(Request $request, TacheRepository $repository, PaginatorInterface $paginator, SessionInterface $session, ManagerRegistry $doctrine): Response
    {
        $userId = $request->getSession()->get('user_id');
        //get user
                $userRepository = $doctrine->getRepository(enduser::class);
                $users = $userRepository->findOneBy(['id_user' => $userId]);
        $defaultOrderBy = 'date_FT';
        $orderBy = $request->query->get('orderBy', 'date_FT'); // Default ordering by date_FT
        $queryBuilder = $repository->createQueryBuilder('t')->orderBy('t.' . $defaultOrderBy, 'ASC');

        // Set order by based on user input
        switch ($orderBy) {
            case 'titre':
                $queryBuilder->orderBy('t.titre_T', 'ASC');
                break;
            case 'etat':
                $queryBuilder->orderBy('t.etat_T', 'ASC');
                break;
            case 'date_FT':
            default:
                $queryBuilder->orderBy('t.date_FT', 'ASC');
                break;
        }

        // Set order direction
        $orderDirection = 'ASC';

        // Get start and end dates from the query parameters if provided
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        // If start and end dates are provided, apply date filtering
        if ($startDate && $endDate) {
            $queryBuilder
                ->andWhere('t.date_FT >= :startDate')
                ->andWhere('t.date_FT <= :endDate')
                ->setParameter('startDate', new \DateTime($startDate))
                ->setParameter('endDate', new \DateTime($endDate));
        }

        $query = $queryBuilder->getQuery();

        // Paginate the query
        $tasks = $paginator->paginate(
            $query, // Doctrine Query object
            $request->query->getInt('page', 1), // Page number
            3// Limit per page
        );

        // Calculate task counts for different "Etat" (status)
        $tasksDoneCount = $repository->countByEtat('DONE');
        $tasksDoingCount = $repository->countByEtat('DOING');
        $tasksToDoCount = $repository->countByEtat('TODO');

        $successMessage = $session->getFlashBag()->get('success');

        return $this->render('tache/listdir.html.twig', [
            'tasks' => $tasks,
            'successMessage' => $successMessage ? $successMessage[0] : null,
            'orderBy' => $orderBy,
            'tasksDoneCount' => $tasksDoneCount,
            'tasksDoingCount' => $tasksDoingCount,
            'tasksToDoCount' => $tasksToDoCount,
            'startDate' => $startDate, // Pass the start date to pre-fill the date picker
            'endDate' => $endDate, // Pass the end date to pre-fill the date picker
            'user' => $users,
        ]);
    }

    #[Route('/tache/search', name: 'tache_search', methods: ['GET'])]
    public function search(TacheRepository $tacheRepository, Request $request): JsonResponse
    {
        $query = $request->query->get('q');
        dump($query);

        $results = [];
        if ($query !== null) {
            $results = $tacheRepository->findByNom($query)->getQuery()->getResult();
        }

        $response = [];
        foreach ($results as $result) {
            $response[] = [
                'url' => $this->generateUrl('tache_detail', ['i' => $result->getIdT()]),
                'nom' => $result->getTitreT(),
            ];
        }

        return new JsonResponse($response);
    }


    #[Route('/tache/detail/{i}', name: 'tache_detail')]
    public function detail($i, TacheRepository $rep, SessionInterface $session, ManagerRegistry $doctrine,Request $request): Response
    {
        $userId = $request->getSession()->get('user_id');
//get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $users = $userRepository->findOneBy(['id_user' => $userId]);
        $session->set('user_id', $userId); // Store user ID in session

        // Get the user by ID
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);

        // Check if the user exists
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
        $tache = $rep->find($i);
        if (!$tache) {
            throw $this->createNotFoundException('Tache Existe Pas');
        }

        return $this->render('tache/detail.html.twig', [
            'tache' => $tache,
            'userId' => $userId,
        ]);
    }

    #[Route('/tache/add', name: 'tache_add')]
public function add(Request $req, ManagerRegistry $doctrine, SessionInterface $session): Response
{
    $userId = $req->getSession()->get('user_id');
    $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);

    if (!$user) {
        throw $this->createNotFoundException('User Existe Pas');
    }

    $x = new tache();
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
            $pieceJointe = $form->get('pieceJointe_T')->getData();
            if ($pieceJointe) {
                $originalFilename = pathinfo($pieceJointe->getClientOriginalName(), PATHINFO_FILENAME);
                $slugger = new AsciiSlugger();
                $safeFilename = $slugger->slug($originalFilename)->lower()->toString();
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pieceJointe->guessExtension();

                try {
                    $pieceJointe->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    
                    // Copy the file to the other directory
                    $targetDirectoryJava = 'C:/Users/amine/Desktop/PiDev/3A5_DevMasters/src/main/resources/assets';
                    $targetPathJava = $targetDirectoryJava . '/' . $newFilename;
                    copy($this->getParameter('uploads_directory').'/'.$newFilename, $targetPathJava);
                } catch (FileException $e) {
                    // Handle exception if necessary
                }

                $x->setPieceJointeT($newFilename);
            }

            // Get the selected etat_T value from the form
            $selectedEtatT = $form->get('etat_T')->getData();

            // Set the etat_T property of the tache entity
            $x->setEtatT($selectedEtatT);

            $em->persist($x);
            $em->flush();

            $session->getFlashBag()->add('success', 'Tâche ajoutée avec succès!');
            return $this->redirectToRoute('tache_list');
        }
    }

    // Pass the image path to the template
    $imagePath = $x->getPieceJointeT() ? $this->getParameter('uploads_directory') . '/' . $x->getPieceJointeT() : null;

    return $this->renderForm('tache/add.html.twig', ['f' => $form, 'imagePath' => $imagePath]);
}

    #[Route('/tache/adddir', name: 'tache_adddir')]
    public function adddir(Request $req, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $userId = $req->getSession()->get('user_id');
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User Existe Pas');
        }

        $x = new tache();
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
                $pieceJointe = $form->get('pieceJointe_T')->getData();
                if ($pieceJointe) {
                    $originalFilename = pathinfo($pieceJointe->getClientOriginalName(), PATHINFO_FILENAME);
                    $slugger = new AsciiSlugger();
                    $safeFilename = $slugger->slug($originalFilename)->lower()->toString();
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$pieceJointe->guessExtension();
    
                    try {
                        $pieceJointe->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                        
                        // Copy the file to the other directory
                        $targetDirectoryJava = 'C:/Users/amine/Desktop/PiDev/3A5_DevMasters/src/main/resources/assets';
                        $targetPathJava = $targetDirectoryJava . '/' . $newFilename;
                        copy($this->getParameter('uploads_directory').'/'.$newFilename, $targetPathJava);
                    } catch (FileException $e) {
                        
                    }
    
                    $x->setPieceJointeT($newFilename);
                }
    
                // Get the selected etat_T value from the form
                $selectedEtatT = $form->get('etat_T')->getData();

                // Set the etat_T property of the tache entity
                $x->setEtatT($selectedEtatT);

                $em = $doctrine->getManager();
                $em->persist($x);
                $em->flush();

                $session->getFlashBag()->add('success', 'Tâche ajoutée avec succès!');
                return $this->redirectToRoute('tache_listdir');
            }

        }
        // Pass the image path to the template
        $imagePath = $x->getPieceJointeT() ? $this->getParameter('uploads_directory') . '/' . $x->getPieceJointeT() : null;

        return $this->renderForm('tache/adddir.html.twig', ['f' => $form, 'imagePath' => $imagePath,
    'user' => $user]);
    }

    #[Route('/tache/update/{i}', name: 'tache_update')]
    public function update($i, TacheRepository $rep, Request $req, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $x = $rep->find($i);
        $form = $this->createForm(TacheType::class, $x);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

                // Handle file upload
                $pieceJointe = $form->get('pieceJointe_T')->getData();
                if ($pieceJointe) {
                    $originalFilename = pathinfo($pieceJointe->getClientOriginalName(), PATHINFO_FILENAME);
                    $slugger = new AsciiSlugger();
                    $safeFilename = $slugger->slug($originalFilename)->lower()->toString();
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$pieceJointe->guessExtension();
    
                    try {
                        $pieceJointe->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                        
                        // Copy the file to the other directory
                        $targetDirectoryJava = 'C:/Users/amine/Desktop/PiDev/3A5_DevMasters/src/main/resources/assets';
                        $targetPathJava = $targetDirectoryJava . '/' . $newFilename;
                        copy($this->getParameter('uploads_directory').'/'.$newFilename, $targetPathJava);
                    } catch (FileException $e) {
                        
                    }
            }

            // Get the selected etat_T value from the form
            $selectedEtatT = $form->get('etat_T')->getData();

            // Set the etat_T property of the tache entity
            $x->setEtatT($selectedEtatT);

            $em = $doctrine->getManager();
            $em->flush();

            $session->getFlashBag()->add('success', 'Tâche mise à jour avec succès!');
            return $this->redirectToRoute('tache_list');
        }

        // Pass the image path to the template
        $imagePath = $x->getPieceJointeT() ? $this->getParameter('uploads_directory') . '/' . $x->getPieceJointeT() : null;

        return $this->renderForm('tache/add.html.twig', ['f' => $form, 'imagePath' => $imagePath]);
    }

    #[Route('/tache/updatedir/{i}', name: 'tache_updatedir')]
    public function updatedir($i, TacheRepository $rep, Request $req, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $userId = $req->getSession()->get('user_id');
        //get user
                $userRepository = $doctrine->getRepository(enduser::class);
                $users = $userRepository->findOneBy(['id_user' => $userId]);
        $x = $rep->find($i);
        $form = $this->createForm(TacheType::class, $x);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

                // Handle file upload
                $pieceJointe = $form->get('pieceJointe_T')->getData();
                if ($pieceJointe) {
                    $originalFilename = pathinfo($pieceJointe->getClientOriginalName(), PATHINFO_FILENAME);
                    $slugger = new AsciiSlugger();
                    $safeFilename = $slugger->slug($originalFilename)->lower()->toString();
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$pieceJointe->guessExtension();
    
                    try {
                        $pieceJointe->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                        
                        // Copy the file to the other directory
                        $targetDirectoryJava = 'C:/Users/amine/Desktop/PiDev/3A5_DevMasters/src/main/resources/assets';
                        $targetPathJava = $targetDirectoryJava . '/' . $newFilename;
                        copy($this->getParameter('uploads_directory').'/'.$newFilename, $targetPathJava);
                    } catch (FileException $e) {
                        
                    }
            }

            // Get the selected etat_T value from the form
            $selectedEtatT = $form->get('etat_T')->getData();

            // Set the etat_T property of the tache entity
            $x->setEtatT($selectedEtatT);

            $em = $doctrine->getManager();
            $em->flush();

            $session->getFlashBag()->add('success', 'Tâche mise à jour avec succès!');
            return $this->redirectToRoute('tache_listdir');
        }

        // Pass the image path to the template
        $imagePath = $x->getPieceJointeT() ? $this->getParameter('uploads_directory') . '/' . $x->getPieceJointeT() : null;

        return $this->renderForm('tache/adddir.html.twig', ['f' => $form, 'imagePath' => $imagePath,
    'user' => $users, ]);
    }

    #[Route('/tache/delete/{i}', name: 'tache_delete')]
    public function delete($i, TacheRepository $rep, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $xs = $rep->find($i);
        $em = $doctrine->getManager();
        $em->remove($xs);
        $em->flush();

        $session->getFlashBag()->add('success', 'Tâche supprimée avec succès!');
        return $this->redirectToRoute('tache_list');
    }

    #[Route('/tache/deletedir/{i}', name: 'tache_deletedir')]
    public function deletedir($i, TacheRepository $rep, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $xs = $rep->find($i);
        $em = $doctrine->getManager();
        $em->remove($xs);
        $em->flush();

        $session->getFlashBag()->add('success', 'Tâche supprimée avec succès!');
        return $this->redirectToRoute('tache_listdir');
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
            $data[] = ['user_name' => $userName,
                'task_count' => $taskCount];
        }

        return $this->render('tache/piechart.html.twig', [
            'data' => $data, // Pass data to twig template
        ]);
    }

    #[Route('/tache/piechartdir', name: 'tache_piechartdir')]
    public function pieChartdir(TacheRepository $tacheRepository,ManagerRegistry $doctrine,Request $request): Response
    {
        $userId = $request->getSession()->get('user_id');
        //get user
                $userRepository = $doctrine->getRepository(enduser::class);
                $users = $userRepository->findOneBy(['id_user' => $userId]);
        // Get the count of tasks done by each user
        $usersTasksCount = $tacheRepository->getUsersTasksCount();

        // Extract user names and task counts from the result
        $data = [];
        foreach ($usersTasksCount as $result) {
            $userName = $result['user_name'];
            $taskCount = $result['task_count'];
            $data[] = ['user_name' => $userName,
                'task_count' => $taskCount];
        }

        return $this->render('tache/piechartdir.html.twig', [
            'data' => $data, // Pass data to twig template
            'user' => $users,
        ]);
    }

    #[Route('/tache/listfront', name: 'tache_listfront')]
    public function listfront(Request $request, TacheRepository $repository, SessionInterface $session, ManagerRegistry $doctrine): Response
    {
        $userId = $request->getSession()->get('user_id');
        //get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $users = $userRepository->findOneBy(['id_user' => $userId]);
        $session->set('user_id', $userId); // Store user ID in session

        // Get the user by ID
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);

        // Check if the user exists


        // Get the type of the current user
        $typeUser = $user->getTypeUser();

        // Store the user type in the session
        $session->set('user_type', $typeUser);
/*
        // Define the API base URL
        $api_url = 'https://api.quotable.io/';

        // Create a Guzzle HTTP client
        $httpClient = new Client([
            'base_uri' => $api_url,
            'timeout' => 10, // Adjust timeout as needed
        ]);
*/
        // Fetch taches based on the current user's category
        $taches = [];
        if ($typeUser === "Responsable employé" || $typeUser === "Employé") {
            // Assuming "nom_Cat" is the field that corresponds to the category in the tache entity
            $taches = $repository->findBy(['nom_Cat' => $typeUser], ['date_FT' => 'ASC']);
        }
        // Get flash message from the request
        $flashMessage = $request->query->get('flash_message');
/*
        // Make the API request to get a random quote
        try {
            $response = $httpClient->get('random');
            $quoteData = json_decode($response->getBody()->getContents(), true);
            $quote = isset($quoteData['content']) ? $quoteData : null;
        } catch (RequestException $e) {
            // Log or handle the error as needed
            $quote = null;
        }
*/
        return $this->render('tache/listfront.html.twig', [
            'taches' => $taches,
            'flash_message' => $flashMessage,
            'user_type' => $typeUser,
            //'quote' => $quote, // Pass the quote to the Twig template
            'user' => $users,
        ]);
    }

    #[Route('/tache/listfront/detail/{i}', name: 'tache_detail_front')]
    public function detailfront($i, Request $request, TacheRepository $rep, SessionInterface $session): Response
    {
        $userId = $session->get('user_id');
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);

        // Retrieve user type from session
        $typeUser = $session->get('user_type');

        $tache = $rep->find($i);
        if (!$tache) {
            throw $this->createNotFoundException('Tache Existe Pas');
        }

        // Create a new CommentaireTache entity
        $comment = new CommentaireTache();
        $comment->setIdUser($user);
        $commentForm = $this->createForm(CommentaireTacheType::class, $comment);

        // Handle the comment form submission
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            // Associate the comment with the tache entity
            $comment->setIdT($tache);
            $comment->setDateC(new \DateTime()); // Set current date

            // Persist and flush the comment entity
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            // Redirect or return a response
            return $this->redirectToRoute('tache_detail_front', ['i' => $i]);
        }

        // Pass the comment form and tache details to the Twig template
        return $this->render('tache/detailfront.html.twig', [
            'tache' => $tache,
            'commentForm' => $commentForm->createView(),
            'userId' => $userId,
            'user_type' => $typeUser,
            'user' => $user,
        ]);
    }

    #[Route('/update-tache-state/{tacheId}/{newState}', name: 'update_tache_state')]
    public function updateTacheState(Request $request, int $tacheId, string $newState, SessionInterface $session): JsonResponse
    {
        // Get the user ID from the session
        $userId = $session->get('user_id');

        // Get the user entity from the database based on the user ID
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);

        $entityManager = $this->getDoctrine()->getManager();
        $tache = $entityManager->getRepository(Tache::class)->find($tacheId);

        if (!$tache) {
            return new JsonResponse(['error' => 'Tache Existe Pas'], Response::HTTP_NOT_FOUND);
        }

        // Update etat_T attribute of the tache entity
        $tache->setEtatT($newState);
        // Update id_user if the task is moved to the "DONE" state
        if ($newState === 'DONE') {

            if ($user) {
                $tache->setIdUser($user);
            } else {
                return new JsonResponse(['error' => 'User Existe Pas'], Response::HTTP_NOT_FOUND);
            }
        }

        try {
            $entityManager->flush(); // Save changes to the database
            return new JsonResponse(['message' => 'Tache state updated successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to update tache state'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/tache/listfront/download-csv', name: 'tache_download_csv')]
    public function downloadCsv(TacheRepository $repository, SessionInterface $session): Response
    {
        // Retrieve user type from session
        $typeUser = $session->get('user_type');

        // Fetch tasks associated with the current user type
        $tasks = [];
        if ($typeUser === "Responsable employé" || $typeUser === "Employé") {
            $tasks = $repository->findBy(['nom_Cat' => $typeUser]);
        }

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        // Get the active sheet
        $sheet = $spreadsheet->getActiveSheet();

        // Set title cell styles
        $titleStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => '012545'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        // Set headers
        $sheet->setCellValue('A1', 'titre_T')->getStyle('A1')->applyFromArray($titleStyle);
        $sheet->setCellValue('B1', 'pieceJointe_T')->getStyle('B1')->applyFromArray($titleStyle);
        $sheet->setCellValue('C1', 'date_DT')->getStyle('C1')->applyFromArray($titleStyle);
        $sheet->setCellValue('D1', 'date_FT')->getStyle('D1')->applyFromArray($titleStyle);
        $sheet->setCellValue('E1', 'desc_T')->getStyle('E1')->applyFromArray($titleStyle);
        $sheet->setCellValue('F1', 'etat_T')->getStyle('F1')->applyFromArray($titleStyle);

        // Populate data
// Populate data
        $row = 2;
        foreach ($tasks as $task) {
            $sheet->setCellValue('A' . $row, $task->getTitreT());

            // Check if the task has a piece jointe
            if ($task->getPieceJointeT() !== null) {
                // Create hyperlink for the file name
                $uploads_directory = $this->getParameter('uploads_directory');
                $hyperlinkUrl = $uploads_directory . '/' . $task->getPieceJointeT();
                $hyperlinkText = $task->getPieceJointeT();
                $sheet->getCell('B' . $row)->getHyperlink()->setUrl($hyperlinkUrl);
                $sheet->getCell('B' . $row)->setValue($hyperlinkText);
            } else {
                // Set the cell value to empty if no piece jointe
                $sheet->setCellValue('B' . $row, ''); // Set the cell value to empty
            }

            // Set other cell values
            $sheet->setCellValue('C' . $row, $task->getDateDT()->format('Y-m-d'));
            $sheet->setCellValue('D' . $row, $task->getDateFT()->format('Y-m-d'));
            $sheet->setCellValue('E' . $row, $task->getDescT());
            $sheet->setCellValue('F' . $row, $task->getEtatT()); // Add this line for etat_T

            // Apply cell styles
            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create a writer
        $writer = new Xlsx($spreadsheet);

        // Save the file to a temporary location
        $tempFilePath = tempnam(sys_get_temp_dir(), 'tasks');
        $writer->save($tempFilePath);

        // Create a response object
        $response = new Response(file_get_contents($tempFilePath));
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Set the filename with current date and time
        $filename = 'tasks_' . date('Y-m-d_H-i-s') . '.xlsx';
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');

        $response->headers->set('Cache-Control', 'max-age=0');

        // Delete the temporary file
        unlink($tempFilePath);

        return $response;
    }

    #[Route('/tache/listfront/import-csv', name: 'tache_import_csv')]
    public function importCsv(Request $request, TacheRepository $repository, SessionInterface $session, ManagerRegistry $doctrine): Response
    {
        $userId = $session->get('user_id');

        // Get the user by ID
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);

        // Check if the user exists

        // Retrieve user type from session
        $typeUser = $session->get('user_type');



        // Check if the form is submitted and valid
        if ($request->isMethod('POST')) {
            // Get the uploaded CSV file
            $uploadedFile = $request->files->get('csv_file');

            // Check if a file was uploaded
            if ($uploadedFile && $uploadedFile->isValid()) {
                // Create a CSV reader instance
                $csvReader = Reader::createFromPath($uploadedFile->getPathname());
                $csvReader->setHeaderOffset(0); // Skip header row

                // Get CSV records
                $records = $csvReader->getRecords();

                // Start importing data into the database
                $entityManager = $this->getDoctrine()->getManager();
                foreach ($records as $record) {
                    // Create a new task entity for each record
                    $task = new Tache();

                    // Check if a task with the same titre_T and nom_Cat already exists
                    $existingTask = $entityManager->getRepository(Tache::class)->findOneBy([
                        'titre_T' => $record['titre_T'],
                        'nom_Cat' => $typeUser, // Assuming $typeUser is used for nom_Cat
                    ]);

                    if ($existingTask) {
                        // Handle existing task
                        // For example, add an error message
                        return $this->redirectToRoute('tache_listfront', ['flash_message' => 'Une tâche avec le même titre et la même catégorie existe déjà !']);
                    } else {
                        // Set properties for the task entity from the current CSV record
                        $task->setTitreT($record['titre_T']);
                        $task->setPieceJointeT($record['pieceJointe_T']);
                        $task->setDateDT(new \DateTime($record['date_DT']));
                        $task->setDateFT(new \DateTime($record['date_FT']));
                        $task->setDescT($record['desc_T']);
                        $task->setEtatT($record['etat_T']);
                        $task->setNomCat($typeUser); // Set category based on user type
                        $task->setIdUser($user);

                        // Persist the task entity
                        $entityManager->persist($task);
                    }
                }

                // Flush the changes to the database
                $entityManager->flush();

                // Redirect to the list page or display a success message
                return $this->redirectToRoute('tache_listfront');
            }
        }

        // If the form is not submitted or the file upload fails, redirect back to the list page
        return $this->redirectToRoute('tache_listfront');
    }

    #[Route('/balbot', name: 'balbot')]
    public function chatbotAction(Request $request, SessionInterface $session)
    {

        $userId = $session->get('user_id');
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);

        // Retrieve user type from session
        $typeUser = $session->get('user_type');
        
        // Get the user message from the request
        $userMessage = $request->request->get('user_message');

        // Create a Guzzle client
        $client = new Client();

        // Make the API request
        $response = $client->request('POST', 'https://chat-gtp-free.p.rapidapi.com/v1/chat/completions', [
            'body' => json_encode([
                "chatId" => "92d97036-3e25-442b-9a25-096ab45b0525",
                "messages" => [
                    [
                        "role" => "system",
                        "content" => "You are a virtual assistant. Your name is BalBot and you are a guide to the baladity project developed by 'Dev Masters' a group of 6 skilled IT Engineers (louati akram - zayane yessine - kaboubi amine - sediri hadil - yahyaoui amine), which is a cross-platform project that simplifies the management of municipalities in Tunisia, as well as the customer service part, in fact our application is intended for employees and citizens, currently we are still under development, this will focus on the Ariana sector.",
                    ],
                    [
                        "role" => "user",
                        "content" => $userMessage, // Use the user message here
                    ],
                ],
            ]),
            'headers' => [
                'X-RapidAPI-Host' => 'chat-gtp-free.p.rapidapi.com',
                'X-RapidAPI-Key' => 'c4ce61e66cmsh093df949f93970dp1167e8jsn5f0ee0024abd',
                'content-type' => 'application/json',
            ],
        ]);

        // Get the response body and decode it
        $responseBody = json_decode($response->getBody()->getContents(), true);

        // Get only the 'txt' attribute from the response
        $displayText = $responseBody['text'] ?? null;

        // Render a Twig template with the response
        return $this->render('tache/balbot.html.twig', [
            'response' => $displayText,
            'userId' => $userId,
            'user_type' => $typeUser,
            'user' => $user,
        ]);
    }
}