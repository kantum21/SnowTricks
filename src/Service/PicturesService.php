<?php

namespace App\Service;

use App\Entity\Picture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PicturesService
{
    private $fileUploader;
    private $entityManager;

    public function __construct(FileUploader $fileUploader, EntityManagerInterface $entityManager)
    {
        $this->fileUploader = $fileUploader;
        $this->entityManager = $entityManager;
    }

    public function savePicture(Picture $picture, UploadedFile $pictureFile)
    {
        if ($pictureFile) {
            $pictureFileName = $this->fileUploader->upload($pictureFile);
            $picture->setPicture($pictureFileName);
        }
        $this->entityManager->persist($picture);
        $this->entityManager->flush();
    }

    public function deletePicture(Picture $picture)
    {
        $this->entityManager->remove($picture);
        $this->entityManager->flush();
    }
}
