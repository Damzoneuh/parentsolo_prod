<?php

namespace App\Repository;

use App\Entity\Flowers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Flowers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Flowers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Flowers[]    findAll()
 * @method Flowers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlowersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flowers::class);
    }

    // /**
    //  * @return Flowers[] Returns an array of Flowers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Flowers
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
