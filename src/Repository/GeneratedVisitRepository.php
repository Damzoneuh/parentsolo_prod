<?php

namespace App\Repository;

use App\Entity\GeneratedVisit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GeneratedVisit|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeneratedVisit|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeneratedVisit[]    findAll()
 * @method GeneratedVisit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeneratedVisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeneratedVisit::class);
    }

    public function getOrderByLast(){
        return $this->createQueryBuilder('v')
            ->orderBy('v.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return GeneratedVisit[] Returns an array of GeneratedVisit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GeneratedVisit
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
