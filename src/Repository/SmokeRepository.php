<?php

namespace App\Repository;

use App\Entity\Smoke;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Smoke|null find($id, $lockMode = null, $lockVersion = null)
 * @method Smoke|null findOneBy(array $criteria, array $orderBy = null)
 * @method Smoke[]    findAll()
 * @method Smoke[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmokeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Smoke::class);
    }

    // /**
    //  * @return Smoke[] Returns an array of Smoke objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Smoke
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
