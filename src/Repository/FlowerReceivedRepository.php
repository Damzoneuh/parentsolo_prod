<?php

namespace App\Repository;

use App\Entity\FlowerReceived;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FlowerReceived|null find($id, $lockMode = null, $lockVersion = null)
 * @method FlowerReceived|null findOneBy(array $criteria, array $orderBy = null)
 * @method FlowerReceived[]    findAll()
 * @method FlowerReceived[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlowerReceivedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlowerReceived::class);
    }

    // /**
    //  * @return FlowerReceived[] Returns an array of FlowerReceived objects
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
    public function findOneBySomeField($value): ?FlowerReceived
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
