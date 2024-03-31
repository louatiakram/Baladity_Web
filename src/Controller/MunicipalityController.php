<?php

namespace App\Controller;

use App\Entity\muni;
use App\Form\AjoutMuniFormType;
use App\Repository\MunicipalityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            $entityManager = $doctrine->getManager();
            $entityManager->persist($muni);
            $entityManager->flush();

            return $this->redirectToRoute('afficher_muni');
        }

        return $this->render('municipality/ajouterMuni.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/afficherMuni', name: 'afficher_muni')]
    public function afficherMuni(MunicipalityRepository $Rep): Response
    {
        $muni = $Rep->findAll();
        return $this->render('municipality/afficherMunicipality.html.twig', [
            'controller_name' => 'AuthorController',
            'munis' => $muni
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
