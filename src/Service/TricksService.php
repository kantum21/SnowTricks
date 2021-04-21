<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\ORM\EntityManager;

class TricksService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function saveComments(Comment $comment, Trick $trick, User $user)
    {
        $comment->setCreatedAt(new \DateTime('now'));
        $comment->setTrick($trick);
        $comment->setUser($user);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }
}
