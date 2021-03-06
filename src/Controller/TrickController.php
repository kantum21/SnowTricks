<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickFormType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Service\TricksService;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     *
     * @param TrickRepository $trickRepository
     * @return Response
     */
    public function homepage(TrickRepository $trickRepository)
    {
        $tricks = $trickRepository->findFirstsTricksOrderedByCreatedAt();

        return $this->render('trick/homepage.html.twig', [
            'tricks' => $tricks,
        ]);
    }

    /**
     * @Route("/loadMoreTricks", name="load_more_tricks")
     *
     * @param TrickRepository $trickRepository
     * @param Request $request
     * @return Response
     */
    public function loadMoreTricks(TrickRepository $trickRepository, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $tricks = $trickRepository->findMoreTricksOrderedByCreatedAt($request->get('lastId'));

            return $this->render('trick/load_more.html.twig', [
                'tricks' => $tricks,
            ]);
        } else {
            return $this->redirectToRoute('app_homepage');
        }
    }

    /**
     * @Route("/tricks/details/{slug}", name="trick_show")
     *
     * @param Trick $trick
     * @param CommentRepository $commentRepository
     * @param Request $request
     * @param TricksService $tricksService
     * @return Response
     */
    public function show(Trick $trick, CommentRepository $commentRepository, Request $request, TricksService $tricksService)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('ROLE_USER');
            /** @var Comment $comment */
            $comment = $form->getData();
            $tricksService->saveComments($comment, $trick, $this->getUser());
            $this->addFlash('success', 'Comment saved !');

            return $this->redirectToRoute('trick_show', [
               'slug' => $trick->getSlug(),
            ]);
        }

        return $this->render('trick/show.html.twig', [
           'trick' => $trick,
            'comments' => $commentRepository->getFirstComments($trick->getId()),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/tricks/details/{slug}/loadMoreComments", name="load_more_comments")
     *
     * @param Trick $trick
     * @param CommentRepository $commentRepository
     * @param Request $request
     * @return Response
     */
    public function loadMoreComments(Trick $trick, CommentRepository $commentRepository, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('trick/load_more_comments.html.twig', [
                'comments' => $commentRepository->findMoreComments($trick->getId(), $request->get('lastId')),
            ]);
        } else {
            $this->redirectToRoute('app_homepage');
        }
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/tricks/edit/new", name="trick_edit_new")
     *
     * @param Request $request
     * @param TricksService $tricksService
     * @return Response
     *
     * @throws Exception
     */
    public function new(Request $request, TricksService $tricksService)
    {
        $form = $this->createForm(TrickFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Trick $trick */
            $trick = $form->getData();
            $array = $tricksService->validateTrick($trick);
            if (count($array) > 0) {
                $this->addFlash('danger', $array->get(0)->getMessage());
            } else {
                $trick->setCreatedAt(new \DateTime());
                $tricksService->saveTrick($trick);
                $this->addFlash('success', 'Trick saved !');

                return $this->redirectToRoute('app_homepage');
            }
        }

        return $this->render('trick/new.html.twig', [
            'trickForm' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("tricks/edit/{slug}", name="trick_edit")
     *
     * @param Request $request
     * @param Trick $trick
     * @param TricksService $tricksService
     * @return Response
     *
     * @throws Exception
     */
    public function edit(Request $request, Trick $trick, TricksService $tricksService)
    {
        $form = $this->createForm(TrickFormType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Trick $trick */
            $trick = $form->getData();
            $array = $tricksService->validateTrick($trick);
            if (count($array) > 0) {
                $this->addFlash('danger', $array->get(0)->getMessage());
            } else {
                $trick->setUpdatedAt(new \DateTime('now'));
                $tricksService->saveTrick($trick);
                $this->addFlash('success', 'Trick updated !');

                return $this->redirectToRoute('app_homepage');
            }
        }

        return $this->render('trick/edit.html.twig', [
            'trickForm' => $form->createView(),
            'trick' => $trick,
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/tricks/delete/{slug}", name="trick_delete")
     *
     * @param Trick $trick
     * @param TricksService $tricksService
     * @return RedirectResponse
     */
    public function delete(Trick $trick, TricksService $tricksService)
    {
        $tricksService->deleteTrick($trick);
        $this->addFlash('success', 'Trick deleted !');

        return $this->redirectToRoute('app_homepage');
    }
}
