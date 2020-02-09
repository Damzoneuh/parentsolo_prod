<?php

namespace App\Repository;

use App\Entity\Silhouette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Silhouette|null find($id, $lockMode = null, $lockVersion = null)
 * @method Silhouette|null findOneBy(array $criteria, array $orderBy = null)
 * @method Silhouette[]    findAll()
 * @method Silhouette[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SilhouetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Silhouette::class);
    }

    // /**
    //  * @return Silhouette[] Returns an array of Silhouette objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Silhouette
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
