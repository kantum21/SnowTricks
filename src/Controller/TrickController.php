<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickFormType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     * @param TrickRepository $trickRepository
     * @return Response
     */
    public function homepage(TrickRepository $trickRepository)
    {
        $tricks = $trickRepository->findFirstsTricksOrderedByCreatedAt();
        return $this->render('trick/homepage.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/loadMoreTricks", name="load_more_tricks")
     * @param TrickRepository $trickRepository
     * @param Request $request
     * @param int $offset
     * @return Response
     */
    public function loadMoreTricks(TrickRepository $trickRepository, Request $request, $offset = 10)
    {
        if ($request->isXmlHttpRequest())
        {
            $tricks = $trickRepository->findMoreTricksOrderedByCreatedAt($offset);
            return $this->render('trick/load_more.html.twig', [
                'tricks' => $tricks
            ]);
        }
        else
        {
            return $this->redirectToRoute('app_homepage');
        }

    }

    /**
     * @Route("/tricks/details/{slug}", name="trick_show")
     * @param Trick $trick
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws Exception
     */
    public function show(Trick $trick, Request $request, EntityManagerInterface $entityManager)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var Comment $comment */
            $comment = $form->getData();
            $comment->setCreatedAt(new \DateTime('now'));
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Comment saved !');

            return $this->redirectToRoute('trick_show', [
               'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/show.html.twig', [
           'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tricks/details/{slug}/loadMoreComments", name="load_more_comments")
     * @param Trick $trick
     * @param Request $request
     * @return Response
     */
    public function loadMoreComments(Trick $trick, Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            return $this->render('trick/load_more_comments.html.twig', [
                'trick' => $trick
            ]);
        }
        else
        {
            $this->redirectToRoute('app_homepage');
        }

    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/tricks/edit/new", name="trick_edit_new")
     * @param Request $request
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(TrickFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Trick $trick */
            $trick = $form->getData();
            $trick->setSlug($slugger->slug(strtolower($trick->getName())));
            $trick->setCreatedAt(new \DateTime());
            $entityManager->persist($trick);
            $entityManager->flush();
            $this->addFlash('success', 'Trick saved !');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('trick/new.html.twig', [
            'trickForm' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("tricks/edit/{slug}", name="trick_edit")
     * @param Request $request
     * @param Trick $trick
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, Trick $trick, SluggerInterface $slugger, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(TrickFormType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var Trick $trick */
            $trick = $form->getData();
            $trick->setSlug($slugger->slug(strtolower($trick->getName())));
            $trick->setUpdatedAt(new \DateTime());
            $entityManager->persist($trick);
            $entityManager->flush();
            $this->addFlash('success', 'Trick updated !');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('trick/edit.html.twig', [
            'trickForm' => $form->createView(),
            'trick' => $trick
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/tricks/delete/{slug}", name="trick_delete")
     * @param Trick $trick
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function delete(Trick $trick, EntityManagerInterface $entityManager)
    {
        foreach ($trick->getPictures() as $picture)
        {
            $trick->removePicture($picture);
        }
        foreach ($trick->getVideos() as $video)
        {
            $trick->removeVideo($video);
        }
        $entityManager->remove($trick);
        $entityManager->flush();
        $this->addFlash('success', 'Trick deleted !');
        return $this->redirectToRoute('app_homepage');
    }
}
