<?php

namespace App\Service;

use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;

class VideosService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function saveVideo(Video $video)
    {
        $this->entityManager->persist($video);
        $this->entityManager->flush();
    }

    public function deleteVideo(Video $video)
    {
        $this->entityManager->remove($video);
        $this->entityManager->flush();
    }
}
