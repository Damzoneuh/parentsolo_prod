<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getAge(User $user, $minAge, $maxAge){
        $date = new \DateTime('now');
        $age = $date->diff($user->getBirthdate(), $date);
        dump($age); die();
    }


    public function excludeCurrentUser(User $user){
        return $this->createQueryBuilder('u')
            ->where('u.id != :user')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->getResult();
    }


    public function getActiveUsers(){
        return $this->createQueryBuilder('u')
            ->andWhere('u.isValidated = :validated')
            ->andWhere('u.isConfirmed = :confirmed')
            ->setParameter('validated', true)
            ->setParameter('confirmed', true)
            ->getQuery()
            ->getResult();
    }

    public function getTextToValidate(){
        return $this->createQueryBuilder('u')
            ->join('u.profil', 'p')
            ->join('p.description', 'd')
            ->andWhere('d.isValidated =:true')
            ->setParameter('true', false)
            ->getQuery()
            ->getResult();
    }

    public function getNotifiedUsers(){
        return $this->createQueryBuilder('u')
            ->andWhere('u.isNotified =:true')
            ->andWhere('u.isDeleted=:false')
            ->setParameter('true', true)
            ->setParameter('false', false)
            ->getQuery()
            ->getResult();
    }

    public function getRenewableUsers(){
        return $this->createQueryBuilder('u')
            ->andWhere('u.isDeleted =:false')
            ->andWhere('u.subscribe != :notNull')
            ->setParameter('false', false)
            ->setParameter('notNull', null)
            ->getQuery()
            ->getResult();
    }

    public function getUserFluent($by, $node){
        return $this->createQueryBuilder('u')
            ->andWhere('u.'.$by.' LIKE :node')
            ->setParameter('node', '%' . $node . '%')
            ->getQuery()
            ->getResult();
    }

    public function getHelvetica(){
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_HELVETICA%')
            ->getQuery()
            ->getResult();
    }
}
