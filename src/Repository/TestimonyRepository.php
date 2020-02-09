<?php

namespace App\Repository;

use App\Entity\Testimony;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Testimony|null find($id, $lockMode = null, $lockVersion = null)
 * @method Testimony|null findOneBy(array $criteria, array $orderBy = null)
 * @method Testimony[]    findAll()
 * @method Testimony[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestimonyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Testimony::class);
    }

     /**
      * @return Testimony[] Returns an array of Testimony objects
      */

    public function getActualAndValidate()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.isValidated = :val')
            ->setParameter('val', true)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Testimony
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
