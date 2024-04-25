<?php

namespace App\Controller;

use App\Entity\muni;
use App\Form\AjoutMuniFormType;
use App\Form\EditMuniFormType;
use App\Repository\MunicipalityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MunicipalityController extends AbstractController
{
    #[Route('/municipality', name: 'app_municipality')]
    public function index(): Response
    {
        return $this->render('municipality/index.html.twig', [
            'controller_name' => 'MunicipalityController',
        ]);
    }

    #[Route('/ajoutMunicipality', name: 'ajouter_municipality')]
    public function ajout(ManagerRegistry $doctrine, Request $request): Response
    {
        $muni = new muni();

        $form = $this->createForm(AjoutMuniFormType::class, $muni);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('imagee_user')->getData();
            if ($image) {
                // Handle image upload and persist its filename to the database
                $fileName = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move($this->getParameter('uploadsDirectory'), $fileName);
                    // Set the image filename to the user entity
                    $muni->setImageeuser($fileName);
                } catch (FileException $e) {
                    // Handle the exception if file upload fails
                    // For example, log the error or display a flash message
                }
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($muni);
            $entityManager->flush();

            return $this->redirectToRoute('afficher_muni');
        }

        return $this->render('municipality/ajouterMuni.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/listMuni', name: 'afficher_muni')]
    public function afficherMuni(Request $request, MunicipalityRepository $repository, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $repository->createQueryBuilder('m');
        $munis = $paginator->paginate(
        $queryBuilder->getQuery(),
        $request->query->getInt('page', 1), // Current page number, default is 1
        5 // Number of items per page
    );
    
        return $this->render('municipality/afficherMunicipality.html.twig', [
            'controller_name' => 'AuthorController',
            'munis' => $munis
        ]);
    }

    #[Route('/listMunicipalité/detail/{i}', name: 'muni_detail')]
    public function detail($i, MunicipalityRepository $rep): Response
    {
        $muni = $rep->find($i);
        if (!$muni) {
            throw $this->createNotFoundException('Task not found');
        }

        return $this->render('municipality/detail.html.twig', ['muni' => $muni]);
    }

    #[Route('/update/{i}', name: 'update_muni')]
    public function modifierMuni($i, ManagerRegistry $doctrine, Request $request): Response

{
    $entityManager = $doctrine->getManager();
    $municipality = $entityManager->getRepository(muni::class)->find($i);

    if (!$municipality) {
        throw $this->createNotFoundException('Municipality not found');
    }

    // Create the form for modifying the actualite
    $form = $this->createForm(AjoutMuniFormType::class, $municipality);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Set the image_a field
        $image = $form->get('imagee_user')->getData();
        if ($image) {
            // Handle image upload and persist its filename to the database
            $fileName = uniqid().'.'.$image->guessExtension();
            try {
                $image->move($this->getParameter('uploadsDirectory'), $fileName);
                // Set the image filename to the user entity
                $municipality->setImageeuser($fileName);
            } catch (FileException $e) {
                // Handle the exception if file upload fails
                // For example, log the error or display a flash message
            }
        }

        // Persist the modified actualite object to the database
        $entityManager->flush();

        // Redirect to a success page or display a success message
        // For example:
        return $this->redirectToRoute('afficher_muni');
    }

    return $this->render('municipality/edit.html.twig', [
        'form' => $form->createView(),
        'muni' => $municipality,
    ]);
}

    #[Route('/Municipality/delete/{i}', name: 'muni_delete')]
    public function delete($i, MunicipalityRepository $rep, ManagerRegistry $doctrine): Response
    {
        $xs = $rep->find($i);
        $em = $doctrine->getManager();
        $em->remove($xs);
        $em->flush();
        return $this->redirectToRoute('afficher_muni');
    }

}
