<?php

namespace App\Repository;

use App\Entity\ChildGard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ChildGard|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChildGard|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChildGard[]    findAll()
 * @method ChildGard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChildGardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChildGard::class);
    }

    // /**
    //  * @return ChildGard[] Returns an array of ChildGard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ChildGard
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
