<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function getFirstComments($trickId)
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.trick = :trickId')
            ->setParameter('trickId', $trickId)
            ->orderBy('c.id', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(2)
            ->getQuery();

        return $qb->getResult();
    }

    public function findMoreComments($trickId, $lastId)
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.trick = :trickId')
            ->andWhere('c.id < :lastId')
            ->setParameters([
                'trickId' => $trickId,
                'lastId' => $lastId,
            ])
            ->orderBy('c.id', 'DESC')
            ->getQuery();

        return $qb->getResult();
    }
}
