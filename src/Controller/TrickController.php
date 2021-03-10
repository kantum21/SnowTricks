<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickFormType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
        $tricks = $trickRepository->findAllOrderedByCreatedAt();
        return $this->render('trick/homepage.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/tricks/details/{slug}", name="trick_show")
     * @param Trick $trick
     * @return Response
     */
    public function show(Trick $trick)
    {
        return $this->render('trick/show.html.twig', [
           'trick' => $trick
        ]);
    }

    /**
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
