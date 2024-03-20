<?php

namespace App\Controller;

use App\Entity\commentairetache;
use App\Entity\enduser;
use App\Entity\tache;
use App\Form\CommentaireTacheType;
use App\Repository\CommentaireTacheRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireTacheController extends AbstractController
{
    #[Route('/commentairetache', name: 'app_commentairetache')]
    public function index(CommentaireTacheRepository $r): Response
    {
        $xs = $r->findAll();
        return $this->render('commentairetache/list.html.twig', ['l' => $xs,]);
    }

    #[Route('/commentairetache/list', name: 'commentairetache_list')]
    public function list(Request $request, CommentaireTacheRepository $repository): Response
    {
        $query = $request->query->get('query');

        // If a search query is provided, filter cmnts based on the title
        if ($query) {
            $cmnts = $repository->findByCommentaire($query); // Replace with appropriate method
        } else {
            // If no search query is provided, fetch all cmnts
            $cmnts = $repository->findAll();
        }

        return $this->render('commentairetache/list.html.twig', [
            'l' => $cmnts,
            'query' => $query, // Pass the query to the template for displaying in the search bar
        ]);
    }
    #[Route('/commentairetache/add', name: 'commentairetache_add')]
    public function add(Request $req, ManagerRegistry $doctrine): Response
    {
        $userId = 50; // Assuming the user ID is 50
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);
        $tacheId = 221; // Assuming the user ID is 50
        $tache = $this->getDoctrine()->getRepository(tache::class)->find($tacheId);

        if (!$user) {
            throw $this->createNotFoundException('User Id not found');
        }
        if (!$tache) {
            throw $this->createNotFoundException('Tache Id not found');
        }

        $x= new commentairetache();
        $x->setIdUser($user);
        $x->setIdT($tache);

        $form = $this->createForm(CommentaireTacheType::class, $x);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {

            $em = $doctrine->getManager();
            $em->persist($x);
            $em->flush();
            return $this->redirectToRoute('commentairetache_list');
        }
        return $this->renderForm('commentairetache/add.html.twig', ['f' => $form,]);
    }

    #[Route('/commentairetache/update/{i}', name: 'commentairetache_update')]
    public function update($i, CommentaireTacheRepository $rep, Request $req, ManagerRegistry $doctrine): Response
    {
        $x = $rep->find($i);
        $form = $this->createForm(CommentaireTacheType::class, $x);
        $form->handleRequest($req);

        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('commentairetache_list');
        }
        return $this->renderForm('commentairetache/add.html.twig', ['f' => $form,]);
    }
    #[Route('/commentairetache/delete/{i}', name: 'commentairetache_delete')]
    public function delete($i, CommentaireTacheRepository $rep, ManagerRegistry $doctrine): Response
    {
        $xs = $rep->find($i);
        $em = $doctrine->getManager();
        $em->remove($xs);
        $em->flush();
        return $this->redirectToRoute('commentairetache_list');
    }
}
