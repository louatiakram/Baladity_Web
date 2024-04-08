<?php

namespace App\Controller;

use App\Entity\CommentaireTache;
use App\Entity\enduser;
use App\Entity\tache;
use App\Form\CommentaireTacheType;
use App\Form\TacheType;
use App\Repository\TacheRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class TacheController extends AbstractController
{

    #[Route('/tache', name: 'tache_list')]
    public function list(Request $request, TacheRepository $repository, PaginatorInterface $paginator, SessionInterface $session): Response
    {
        // Fetch all tasks from the repository
        $query = $repository->createQueryBuilder('t')
            ->orderBy('t.date_FT', 'ASC')
            ->getQuery();

        // Paginate the query
        $tasks = $paginator->paginate(
            $query, // Doctrine Query object
            $request->query->getInt('page', 1), // Page number
            6// Limit per page
        );

        $successMessage = $session->getFlashBag()->get('success');

        return $this->render('tache/list.html.twig', [
            'tasks' => $tasks,
            'successMessage' => $successMessage ? $successMessage[0] : null, // Pass the success message if it exists
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
    public function detail($i, TacheRepository $rep): Response
    {
        $userId = 50; // Assuming the user ID is 50
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);
        $tache = $rep->find($i);
        if (!$tache) {
            throw $this->createNotFoundException('Tache Existe Pas');
        }

        return $this->render('tache/detail.html.twig', [
            'tache' => $tache,
            'userId' => $userId,
        ]);
    }

    #[Route('/tache/detailfront/{i}', name: 'tache_detail_front')]
    public function detailfront($i, Request $request, TacheRepository $rep): Response
    {
        $userId = 49; // Assuming the user ID is 50
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('User Existe Pas');
        }

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
        ]);
    }

    #[Route('/tache/add', name: 'tache_add')]
    public function add(Request $req, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $userId = 50; // Assuming the user ID is 50
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
                /** @var UploadedFile|null $pieceJointe */
                $pieceJointe = $form->get('pieceJointe_T')->getData();
                if ($pieceJointe) {
                    $originalFilename = pathinfo($pieceJointe->getClientOriginalName(), PATHINFO_FILENAME);
                    // Move the file to the uploads directory
                    try {
                        $uploadedFile = $pieceJointe->move(
                            $this->getParameter('uploadsDirectory'), // Use the parameter defined in services.yaml
                            $originalFilename . '.' . $pieceJointe->guessExtension()
                        );
                        $x->setPieceJointeT($uploadedFile->getFilename());
                    } catch (FileException $e) {}
                }

                // Get the selected etat_T value from the form
                $selectedEtatT = $form->get('etat_T')->getData();

                // Set the etat_T property of the tache entity
                $x->setEtatT($selectedEtatT);

                $em = $doctrine->getManager();
                $em->persist($x);
                $em->flush();

                $session->getFlashBag()->add('success', 'Tâche ajoutée avec succès!');
                return $this->redirectToRoute('tache_list');
            }

        }
        return $this->renderForm('tache/add.html.twig', ['f' => $form]);
    }

    #[Route('/tache/update/{i}', name: 'tache_update')]
    public function update($i, TacheRepository $rep, Request $req, ManagerRegistry $doctrine, SessionInterface $session): Response
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
                        $originalFilename . '.' . $pieceJointe->guessExtension()
                    );
                    $x->setPieceJointeT($uploadedFile->getFilename());
                } catch (FileException $e) {}
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
        return $this->renderForm('tache/add.html.twig', ['f' => $form]);
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

    #[Route('/tache/listfront', name: 'tache_listfront')]
    public function listfront(Request $request, TacheRepository $repository, SessionInterface $session): Response
    {
        $userId = 58; // You can get the user ID from wherever it's stored
        $session->set('user_id', $userId); // Store user ID in session

        // Get the user by ID
        $user = $this->getDoctrine()->getRepository(EndUser::class)->find($userId);

        // Check if the user exists
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // Get the type of the current user
        $typeUser = $user->getTypeUser();

        // Store the user type in the session
        $session->set('user_type', $typeUser);

        // Fetch taches based on the current user's category
        $taches = [];
        if ($typeUser === "Responsable employé" || $typeUser === "Employé") {
            // Assuming "nom_Cat" is the field that corresponds to the category in the tache entity
            $taches = $repository->findBy(['nom_Cat' => $typeUser], ['date_FT' => 'ASC']);
        }

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
            $data[] = ['user_name' => $userName,
                'task_count' => $taskCount];
        }

        return $this->render('tache/piechart.html.twig', [
            'data' => $data, // Pass data to twig template
        ]);
    }

    #[Route('/tache/download-csv', name: 'tache_download_csv')]
    public function downloadCsv(TacheRepository $repository, SessionInterface $session): Response
    {
        // Retrieve user type from session
        $typeUser = $session->get('user_type');

        if (!$typeUser) {
            // If user type is not found in session, handle the error (redirect or display message)
            // For example:
            throw $this->createNotFoundException('User type not found in session.');
        }

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
        $sheet->setCellValue('A1', 'Titre')->getStyle('A1')->applyFromArray($titleStyle);
        $sheet->setCellValue('B1', 'Pièce Jointe')->getStyle('B1')->applyFromArray($titleStyle);
        $sheet->setCellValue('C1', 'Date Début')->getStyle('C1')->applyFromArray($titleStyle);
        $sheet->setCellValue('D1', 'Date Fin')->getStyle('D1')->applyFromArray($titleStyle);
        $sheet->setCellValue('E1', 'Description')->getStyle('E1')->applyFromArray($titleStyle);
        $sheet->setCellValue('F1', 'Etat')->getStyle('F1')->applyFromArray($titleStyle); // Add this line for etat_T

        // Populate data
        $row = 2;
        foreach ($tasks as $task) {
            $sheet->setCellValue('A' . $row, $task->getTitreT());

            // Check if the task has a piece jointe
            if ($task->getPieceJointeT() !== null) {
                // Create hyperlink for the file name
                $hyperlinkFormula = '=HYPERLINK("C:/Users/ASUS/Desktop/3A5S2/PIDEV/DevMasters-Baladity/public/uploads/' . $task->getPieceJointeT() . '", "' . $task->getPieceJointeT() . '")';
                $sheet->getCell('B' . $row)->setValueExplicit($hyperlinkFormula, DataType::TYPE_FORMULA);
            } else {
                // Set the cell value to empty if no piece jointe
                $sheet->setCellValue('B' . $row, '');
            }

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

}