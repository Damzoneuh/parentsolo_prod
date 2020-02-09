<?php

namespace App\Repository;

use App\Entity\Cook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Cook|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cook|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cook[]    findAll()
 * @method Cook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cook::class);
    }

    // /**
    //  * @return Cook[] Returns an array of Cook objects
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
    public function findOneBySomeField($value): ?Cook
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
