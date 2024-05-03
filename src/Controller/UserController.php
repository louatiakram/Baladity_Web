<?php

namespace App\Controller;

use App\Entity\enduser;
use App\Entity\muni;
use App\Form\AdminEditUserType;
use App\Form\EditProfileType;
use App\Form\RegisterType;
use App\Repository\enduserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
     
    #[Route('/afficher', name: 'afficher_user')]
    public function afficher(Request $request, EnduserRepository $repository, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $repository->createQueryBuilder('u')
            ->where('u.type_user IN (:types)')
            ->setParameter('types', ['Citoyen', 'Directeur', 'Employé', 'Responsable employé']);

        $users = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1), // Current page number, default is 1
            5 // Number of items per page
    );

    return $this->render('user/afficherUser.html.twig', [
        'controller_name' => 'AuthorController',
        'users' => $users
    ]);
}

    #[Route('/afficher/detail/{i}', name: 'user_detail')]
    public function detail($i,Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        //$user = $rep->find($i);
        //get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $user = $userRepository->findOneBy(['id_user' => $i]);

        //get muni name
        $muniId = $user->getIdMuni();
        $muniRepository = $doctrine->getRepository(muni::class);
        $muni = $muniRepository->findOneBy(['id_muni' => $muniId]);
        $muniName = $muni->getNomMuni();

        //edit
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Create the form for modifying the actualite
        $form = $this->createForm(AdminEditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
    
            // Persist the modified actualite object to the database
            $entityManager->flush();
    
            // Redirect to a success page or display a success message
            // For example:
            return $this->redirectToRoute('user_detail', ['i' => $i]);
        }

        return $this->render('user/detail.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'muni' => $muniName,
            'i' => $i,
        ]);
    }

    #edit and display user
    #[Route('/profile', name: 'profile_user')]
    public function profile(Request $request, ManagerRegistry $doctrine): Response
    {

        $entityManager = $doctrine->getManager();

        // Retrieving user ID from the session
        $userId = $request->getSession()->get('user_id');
        //$userId = 81;

        //get user
        $userRepository = $doctrine->getRepository(enduser::class);
        $user = $userRepository->findOneBy(['id_user' => $userId]);

        //get muni name
        $muniId = $user->getIdMuni();
        $muniRepository = $doctrine->getRepository(muni::class);
        $muni = $muniRepository->findOneBy(['id_muni' => $muniId]);
        $muniName = $muni->getNomMuni();


        //edit
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Create the form for modifying the user
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the image_a field
            $image = $form->get('image_user')->getData();
            if ($image) {
                // Handle image upload and persist its filename to the database
                $fileName = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move($this->getParameter('uploadsDirectory'), $fileName);
                    // Set the image filename to the user entity
                    $user->setImageUser($fileName);
                } catch (FileException $e) {
                    // Handle the exception if file upload fails
                    // For example, log the error or display a flash message
                }
            }
    
            // Persist the modified actualite object to the database
            $entityManager->flush();
    
            // Redirect to a success page or display a success message
            // For example:
            return $this->redirectToRoute('profile_user');
        }

        return $this->render('user/profile.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
            'user' => $user,
            'muni' => $muniName,
        ]);
    }

    #[Route('/user/delete/{i}', name: 'user_delete')]
    public function delete($i, enduserRepository $rep, ManagerRegistry $doctrine): Response
    {
        $xs = $rep->find($i);
        $em = $doctrine->getManager();
        $em->remove($xs);
        $em->flush();
        return $this->redirectToRoute('afficher_user');
    }

    #[Route('/user/stat', name: 'stat')]
    public function usersByType(enduserRepository $userRepository): Response
    {
        $citoyen = $userRepository->findByTypeUser('Citoyen');
        $Responsable = $userRepository->findByTypeUser('Responsable employé');
        $Employe = $userRepository->findByTypeUser('Employé');
        $Directeur = $userRepository->findByTypeUser('Directeur'); 

        // Count users per id_muni
        $usersPerMuni = $userRepository->countUsersPerMuni();

        // You can pass $users to your template for rendering

        return $this->render('user/stat.html.twig', [
            'citoyen' => $citoyen,
            'Responsable' => $Responsable,
            'Employe' => $Employe,
            'Directeur' => $Directeur,
            'usersPerMuni' => $usersPerMuni,
        ]);
    }


}
