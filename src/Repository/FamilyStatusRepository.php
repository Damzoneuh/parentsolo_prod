<?php

namespace App\Repository;

use App\Entity\FamilyStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FamilyStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method FamilyStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method FamilyStatus[]    findAll()
 * @method FamilyStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FamilyStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FamilyStatus::class);
    }

    // /**
    //  * @return FamilyStatus[] Returns an array of FamilyStatus objects
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
    public function findOneBySomeField($value): ?FamilyStatus
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
