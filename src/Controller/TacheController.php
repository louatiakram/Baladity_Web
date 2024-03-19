<?php

namespace App\Controller;

use App\Entity\tache;
use App\Form\TacheType;
use App\Entity\enduser;
use App\Repository\TacheRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;


class TacheController extends AbstractController
{
    #[Route('/tache', name: 'app_tache')]
    public function index(TacheRepository $r): Response
    {
        $xs = $r->findAll();
        return $this->render('tache/list.html.twig', ['l' => $xs,]);
    }

    #[Route('/tache/list', name: 'tache_list')]
    public function list(TacheRepository $r): Response
    {
        $xs = $r->findAll();
        return $this->render('tache/list.html.twig', ['l' => $xs,]);
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

}
