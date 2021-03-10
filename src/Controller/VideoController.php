<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    /**
     * @Route("videos/edit/new", name="video_new")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function new(Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(VideoFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var Video $video */
            $video = $form->getData();
            $entityManager->persist($video);
            $entityManager->flush();
            $this->addFlash('success', 'Video saved !');

            return $this->redirectToRoute('trick_edit_new');
        }

        return $this->render('video/new.html.twig', [
            'videoForm' => $form->createView()
        ]);
    }

    /**
     * @Route("videos/edit/{id}", name="video_edit")
     * @param Request $request
     * @param Video $video
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, Video $video, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(VideoFormType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var Video $video */
            $video = $form->getData();
            $entityManager->persist($video);
            $entityManager->flush();
            $this->addFlash('success', 'Video saved !');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('video/edit.html.twig', [
            'videoForm' => $form->createView(),
            'video' => $video
        ]);
    }

    /**
     * @Route("videos/delete/{id}", name="video_delete")
     * @param Video $video
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function delete(Video $video, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($video);
        $entityManager->flush();
        $this->addFlash('success', 'Video deleted !');
        return $this->redirectToRoute('app_homepage');
    }
}
