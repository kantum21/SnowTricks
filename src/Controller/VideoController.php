<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoFormType;
use App\Service\VideosService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class VideoController extends AbstractController
{
    /**
     * @Route("videos/edit/new", name="video_new")
     *
     * @param Request $request
     * @param VideosService $videosService
     * @return RedirectResponse|Response
     */
    public function new(Request $request, VideosService $videosService)
    {
        $form = $this->createForm(VideoFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Video $video */
            $video = $form->getData();
            $videosService->saveVideo($video);
            $this->addFlash('success', 'Video saved !');

            return $this->redirectToRoute('trick_edit_new');
        }

        return $this->render('video/new.html.twig', [
            'videoForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("videos/edit/{id}", name="video_edit")
     *
     * @param Request $request
     * @param Video $video
     * @param VideosService $videosService
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, Video $video, VideosService $videosService)
    {
        $form = $this->createForm(VideoFormType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Video $video */
            $video = $form->getData();
            $videosService->saveVideo($video);
            $this->addFlash('success', 'Video saved !');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('video/edit.html.twig', [
            'videoForm' => $form->createView(),
            'video' => $video,
        ]);
    }

    /**
     * @Route("videos/delete/{id}", name="video_delete")
     *
     * @param Video $video
     * @param VideosService $videosService
     * @return RedirectResponse
     */
    public function delete(Video $video, VideosService $videosService)
    {
        $videosService->deleteVideo($video);
        $this->addFlash('success', 'Video deleted !');

        return $this->redirectToRoute('app_homepage');
    }
}
