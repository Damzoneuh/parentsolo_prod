<?php

namespace App\Repository;

use App\Entity\PaymentProfil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PaymentProfil|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentProfil|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentProfil[]    findAll()
 * @method PaymentProfil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentProfilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentProfil::class);
    }

    // /**
    //  * @return PaymentProfil[] Returns an array of PaymentProfil objects
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
    public function findOneBySomeField($value): ?PaymentProfil
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
