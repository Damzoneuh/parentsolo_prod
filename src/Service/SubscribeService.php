<?php


namespace App\Service;


use App\Entity\Items;
use App\Entity\Payment;
use App\Entity\Subscribe;
use App\Entity\User;
use Doctrine\ORM\EntityManager;

class SubscribeService
{
    private $_em;
    private $_security;
    public function __construct(EntityManager $entityManager)
    {
        $this->_em = $entityManager;
    }

    /**
     * @param User $user
     * @param $id
     * @param $itemId
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setPayPalSubscribe(User $user, $id, $itemId){
        $em = $this->_em;
        $subscribe = new Subscribe();
        $subscribe->setPlan($id);
        $item = $em->getRepository(Items::class)->find($itemId);
        $subscribe->setItem($item);
        $deadline = new \DateTime('+' . $item->getDuration() . 'month');
        $subscribe->setDeadline($deadline);
        $subscribe->setIsAuthorized(true);
        $this->_em->persist($subscribe);
        $user->setRoles(['ROLE_USER', 'ROLE_' . $item->getRole()]);
        $user->setSubscribe($subscribe);
        $user->setUpdatedAt(new \DateTime('now'));
        $payment = new Payment();
        $payment->setUser($user);
        $payment->setSubscribe($subscribe);
        $payment->setMethod('paypal');
        $payment->setUniqKey($subscribe->getPlan());
        $payment->addItem($item);
        $payment->setIsCaptured(true);
        $payment->setIsAccepted(true);
        $payment->setDate(new \DateTime('now'));
        $this->_em->persist($payment);
        $this->_em->persist($user);
        $this->_em->flush();
        self::implementFlowersAsSubscription($user);
        return true;
    }

    /**
     * @param User $user
     * @param $itemId
     * @param $alias
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setSixSubscription(User $user, $itemId, $alias){
        $em = $this->_em;
        $subscribe = new Subscribe();
        $subscribe->setPlan($alias);
        $item = $em->getRepository(Items::class)->find($itemId);
        $subscribe->setItem($item);
        $deadline = new \DateTime('+' . $item->getDuration() . 'month');
        $subscribe->setDeadline($deadline);
        $subscribe->setIsAuthorized(true);
        $this->_em->persist($subscribe);
        $user->setRoles(['ROLE_USER', 'ROLE_' . $item->getRole()]);
        $user->setSubscribe($subscribe);
        $user->setUpdatedAt(new \DateTime('now'));
        $payment = new Payment();
        $payment->setUser($user);
        $payment->setSubscribe($subscribe);
        $payment->setMethod('six');
        $payment->setUniqKey($subscribe->getPlan());
        $payment->addItem($item);
        $payment->setIsCaptured(false);
        $payment->setIsAccepted(true);
        $payment->setDate(new \DateTime('now'));
        $this->_em->persist($payment);
        $this->_em->persist($user);
        $this->_em->flush();
        self::implementFlowersAsSubscription($user);
        return true;
    }

    /**
     * @param User $user
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function implementFlowersAsSubscription(User $user){
        if (in_array('ROLE_MEDIUM', $user->getRoles())){
            ($user->getFlowerNumber() > 0) ?
                $user->setFlowerNumber($user->getFlowerNumber() + 5) :
                $user->setFlowerNumber(5);
            ($user->getFavoriteNumber() > 0) ?
                $user->setFavoriteNumber($user->getFavoriteNumber() + 5) :
                $user->setFavoriteNumber(5);
            $this->_em->flush();
        }
        return true;
    }
}