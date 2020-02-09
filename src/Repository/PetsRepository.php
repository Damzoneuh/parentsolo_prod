<?php

namespace App\Repository;

use App\Entity\Pets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Pets|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pets|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pets[]    findAll()
 * @method Pets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PetsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pets::class);
    }

    // /**
    //  * @return Pets[] Returns an array of Pets objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pets
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
