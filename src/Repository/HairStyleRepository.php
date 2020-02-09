<?php

namespace App\Repository;

use App\Entity\HairStyle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method HairStyle|null find($id, $lockMode = null, $lockVersion = null)
 * @method HairStyle|null findOneBy(array $criteria, array $orderBy = null)
 * @method HairStyle[]    findAll()
 * @method HairStyle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HairStyleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HairStyle::class);
    }

    // /**
    //  * @return HairStyle[] Returns an array of HairStyle objects
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
    public function findOneBySomeField($value): ?HairStyle
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
