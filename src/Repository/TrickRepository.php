<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    public function findFirstsTricksOrderedByCreatedAt()
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.id', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(10)
            ->getQuery();

        return $qb->getResult();
    }

    public function findMoreTricksOrderedByCreatedAt($lastId)
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.id < :lastId')
            ->setParameter('lastId', $lastId)
            ->orderBy('t.id', 'DESC')
            ->getQuery();

        return $qb->getResult();
    }
}
