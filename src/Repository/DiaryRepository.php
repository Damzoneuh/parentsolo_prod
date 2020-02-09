<?php

namespace App\Repository;

use App\Entity\Diary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Diary|null find($id, $lockMode = null, $lockVersion = null)
 * @method Diary|null findOneBy(array $criteria, array $orderBy = null)
 * @method Diary[]    findAll()
 * @method Diary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Diary::class);
    }

    /**
     * @return mixed
     */
    public function findByValidateAndActual()
    {
        return $this->createQueryBuilder('d')
            ->where('d.isValidate = :validate')
            ->setParameter('validate', true)
            ->orderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Diary
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
