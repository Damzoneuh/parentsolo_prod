<?php

namespace App\Repository;

use App\Entity\Hair;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Hair|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hair|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hair[]    findAll()
 * @method Hair[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HairRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hair::class);
    }

    // /**
    //  * @return Hair[] Returns an array of Hair objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Hair
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
