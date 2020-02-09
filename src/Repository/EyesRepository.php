<?php

namespace App\Repository;

use App\Entity\Eyes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Eyes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Eyes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Eyes[]    findAll()
 * @method Eyes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EyesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eyes::class);
    }

    // /**
    //  * @return Eyes[] Returns an array of Eyes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Eyes
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
