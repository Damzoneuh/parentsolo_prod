<?php

namespace App\Repository;

use App\Entity\LifeStyle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LifeStyle|null find($id, $lockMode = null, $lockVersion = null)
 * @method LifeStyle|null findOneBy(array $criteria, array $orderBy = null)
 * @method LifeStyle[]    findAll()
 * @method LifeStyle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LifeStyleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LifeStyle::class);
    }

    // /**
    //  * @return LifeStyle[] Returns an array of LifeStyle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LifeStyle
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
