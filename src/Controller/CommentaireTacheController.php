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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireTacheController extends AbstractController
{
    #[Route('/commentairetache', name: 'app_commentairetache')]
    public function index(CommentaireTacheRepository $r, SessionInterface $session): Response
    {
        $xs = $r->findAll();
        return $this->render('commentairetache/list.html.twig', ['l' => $xs]);
    }

    #[Route('/commentairetache/list', name: 'commentairetache_list')]
    public function list(Request $request, CommentaireTacheRepository $repository, SessionInterface $session): Response
    {
        $query = $request->query->get('query');

        // If a search query is provided, filter cmnts based on the title
        if ($query) {
            $cmnts = $repository->findByCommentaire($query);
        } else {
            // If no search query is provided, fetch all cmnts
            $cmnts = $repository->findAll();
        }

        return $this->render('commentairetache/list.html.twig', [
            'l' => $cmnts,
            'query' => $query,
        ]);
    }

    #[Route('/commentairetache/add/{id}', name: 'commentairetache_add')]
    public function add($id, Request $req, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $userId = $session->get('user_id');
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User Existe Pas');
        }

        $tacheId = $this->getDoctrine()->getRepository(tache::class)->find($id);

        if (!$tacheId) {
            throw $this->createNotFoundException('Tache Existe Pas');
        }

        $x = new commentairetache();
        $x->setIdUser($user);
        $x->setIdT($tacheId);
        $x->setDateC(new \DateTime()); // Set current date

        $form = $this->createForm(CommentaireTacheType::class, $x);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($x);
            $em->flush();

            return $this->redirectToRoute('tache_list');
        }

        return $this->renderForm('commentairetache/add.html.twig', ['f' => $form, 'user' => $user,]);
    }

    #[Route('/commentairetache/update/{i}', name: 'commentairetache_update')]
    public function update($i, CommentaireTacheRepository $rep, Request $req, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $userId = $session->get('user_id');
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User Existe Pas');
        }
        $x = $rep->find($i);
        $x->setDateC(new \DateTime()); // Set current date
        $form = $this->createForm(CommentaireTacheType::class, $x);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('tache_list');
        }
        return $this->renderForm('commentairetache/add.html.twig', ['f' => $form, 'user' => $user,]);
    }

    #[Route('/commentairetache/delete/{i}', name: 'commentairetache_delete')]
    public function delete($i, CommentaireTacheRepository $rep, ManagerRegistry $doctrine): Response
    {
        $xs = $rep->find($i);
        $em = $doctrine->getManager();
        $em->remove($xs);
        $em->flush();
        return $this->redirectToRoute('tache_list');
    }
    #[Route('/commentairetache/adddir/{id}', name: 'commentairetache_adddir')]
    public function adddir($id, Request $req, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $userId = $session->get('user_id');
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User Existe Pas');
        }

        $tacheId = $this->getDoctrine()->getRepository(tache::class)->find($id);

        if (!$tacheId) {
            throw $this->createNotFoundException('Tache Existe Pas');
        }

        $x = new commentairetache();
        $x->setIdUser($user);
        $x->setIdT($tacheId);
        $x->setDateC(new \DateTime()); // Set current date

        $form = $this->createForm(CommentaireTacheType::class, $x);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($x);
            $em->flush();

            return $this->redirectToRoute('tache_listdir');
        }

        return $this->renderForm('commentairetache/adddir.html.twig', ['f' => $form, 'user' => $user,]);
    }

    #[Route('/commentairetache/updatedir/{i}', name: 'commentairetache_updatedir')]
    public function updatedir($i, CommentaireTacheRepository $rep, Request $req, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $userId = $session->get('user_id');
        $user = $this->getDoctrine()->getRepository(enduser::class)->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User Existe Pas');
        }
        $x = $rep->find($i);
        $x->setDateC(new \DateTime()); // Set current date
        $form = $this->createForm(CommentaireTacheType::class, $x);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('tache_listdir');
        }
        return $this->renderForm('commentairetache/adddir.html.twig', ['f' => $form, 'user' => $user,]);
    }

    #[Route('/commentairetache/deletedir/{i}', name: 'commentairetache_deletedir')]
    public function deletedir($i, CommentaireTacheRepository $rep, ManagerRegistry $doctrine): Response
    {
        $xs = $rep->find($i);
        $em = $doctrine->getManager();
        $em->remove($xs);
        $em->flush();
        return $this->redirectToRoute('tache_listdir');
    }

    #[Route('/commentairetache/deletefront/{i}', name: 'commentairetache_deletefront')]
    public function deletefront($i, CommentaireTacheRepository $rep, ManagerRegistry $doctrine): Response
    {
        $xs = $rep->find($i);
        $em = $doctrine->getManager();
        $em->remove($xs);
        $em->flush();
        return $this->redirectToRoute('tache_listfront');
    }
}