<?php


namespace App\Service;

use App\Entity\Items;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class ItemsService
{
    private $_em;

    public function __construct(EntityManager $entityManager)
    {
        $this->_em = $entityManager;
    }

    public function createItem($id, $userId) : bool
    {
        $item = $this->_em->getRepository(Items::class)->find($id);
        $user = $this->_em->getRepository(User::class)->find($userId);
        if (strstr($item->getType(), 'flower')){
            ($user->getFlowerNumber() > 0 || !null) ?
                $user->setFlowerNumber($item->getQuantity() + $user->getFlowerNumber()) :
                $user->setFlowerNumber($item->getQuantity());
        }
        if (strstr($item->getType(), 'favorite')){
            ($user->getFavoriteNumber() > 0 || !null) ?
                $user->setFavoriteNumber($item->getQuantity() + $user->getFavoriteNumber()) :
                $user->setFavoriteNumber($item->getQuantity());
        }
        try {
            $this->_em->persist($user);
        } catch (ORMException $e) {
            return false;
        }
        try {
            $this->_em->flush();
        } catch (OptimisticLockException $e) {
            return false;
        } catch (ORMException $e) {
            return false;
        }
        return true;
    }

    /**
     * @param User $currentUser
     * @param User $user
     * @return bool
     */
    public static function checkFavorite(User $currentUser, User $user) : bool
    {
        $currentFavorite = $currentUser->getProfil()->getFavorite();
        if ($currentFavorite->count() > 0){
            /** @var User $favorite */
            foreach ($currentFavorite->getValues() as $favorite){
                if ($favorite->getId() == $user->getId()){
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public static function checkFlowersRights(User $user) : bool {
        $flowers = $user->getFlowerNumber();
        if ($flowers && $flowers > 0){
            return true;
        }
        return false;
    }
}