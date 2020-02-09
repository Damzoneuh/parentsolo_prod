<?php

namespace App\Repository;

use App\Entity\Childs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Childs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Childs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Childs[]    findAll()
 * @method Childs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChildsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Childs::class);
    }

    // /**
    //  * @return Childs[] Returns an array of Childs objects
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
    public function findOneBySomeField($value): ?Childs
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
