<?php

namespace App\Repository;

use App\Entity\Temperament;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Temperament|null find($id, $lockMode = null, $lockVersion = null)
 * @method Temperament|null findOneBy(array $criteria, array $orderBy = null)
 * @method Temperament[]    findAll()
 * @method Temperament[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemperamentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Temperament::class);
    }

    // /**
    //  * @return Temperament[] Returns an array of Temperament objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Temperament
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
