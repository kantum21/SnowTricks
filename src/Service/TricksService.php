<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TricksService
{
    private $entityManager;
    private $slugger;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
        $this->validator = $validator;
    }

    public function saveComments(Comment $comment, Trick $trick, User $user)
    {
        $comment->setCreatedAt(new \DateTime('now'));
        $comment->setTrick($trick);
        $comment->setUser($user);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    public function validateTrick(Trick $trick)
    {
        $trick->setSlug($this->slugger->slug(strtolower($trick->getName())));

        return $this->validator->validate($trick);
    }

    public function saveTrick(Trick $trick)
    {
        $this->entityManager->persist($trick);
        $this->entityManager->flush();
    }

    public function deleteTrick(Trick $trick)
    {
        foreach ($trick->getPictures() as $picture) {
            $trick->removePicture($picture);
        }
        foreach ($trick->getVideos() as $video) {
            $trick->removeVideo($video);
        }
        $this->entityManager->remove($trick);
        $this->entityManager->flush();
    }
}
