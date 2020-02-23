<?php

namespace App\Repository;

use App\Entity\Messages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Messages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Messages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Messages[]    findAll()
 * @method Messages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messages::class);
    }

    /**
     * @param $id
     * @return Messages[]
     */
    public function findMyMessages($id){
        return $this->createQueryBuilder('m')
            ->andWhere('m.messageTo = :id')
            ->orWhere('m.messageFrom = :id')
            ->setParameter('id', $id)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findInnerMessages($id){
        return $this->createQueryBuilder('m')
            ->andWhere('m.messageTo = :id')
            ->andWhere('m.isRead = :false')
            ->setParameter('id', $id)
            ->setParameter('false', false)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getConversation($user, $target){
        return $this->createQueryBuilder('m')
            ->andWhere('m.messageFrom = :user OR m.messageTo = :user')
            ->andWhere('m.messageTo = :target OR m.messageFrom = :target')
            ->orderBy('m.id', 'ASC')
            ->setParameter('user', $user)
            ->setParameter('target', $target)
            ->getQuery()
            ->getResult();

    }

}
