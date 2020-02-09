<?php

namespace App\Controller;

use App\Entity\Img;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FavoriteController extends AbstractController
{
    /**
     * @Route("/favorite", name="favorite")
     */
    public function index()
    {
        /** @var $user User */
        $user = $this->getUser();
        $data = [];
        if ($user->getImg()->count() > 0){
            $imgs = $user->getImg()->getValues();
            /** @var Img $img */
            foreach ($imgs as $img){
                if ($img->getIsprofile()){
                    $data['profilImg'] = $img->getId();
                }
            }
            if (!isset($data['profilImg'])){
                $data['profilImg'] = null;
            }
        }
        else{
            $data['profilImg'] = null;
        }
        $data['isMan'] = $user->getProfil()->getIsMan();
        $profil = $user->getProfil();
        $data['complete'] = true;

        if (empty($profil->getDescription()) || empty($profil->getActivity())
            || empty($profil->getCook()->getValues()) || empty($profil->getEyes())
            || empty($profil->getHair()) || empty($profil->getHairStyle()) || empty($profil->getHobbies()->getValues()) || empty($profil->getLangages()->getValues())
            || empty($profil->getLifestyle()) || empty($profil->getMusic()->getValues()) || empty($profil->getNationality()) || empty($profil->getOrigin())
            || empty($profil->getOuting()->getValues()) || empty($profil->getReading()->getValues())
            || empty($profil->getSilhouette()) || empty($profil->getSize()) || empty($profil->getSmoke())){
            $data['complete'] = false;
        }

        $data['userId'] = $user->getId();
        return $this->render('favorite/index.html.twig', [
            'data' => $data,
        ]);
    }


    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/user/favorite", name="api_user_favorite", methods={"GET"})
     */
    public function getFavorite(){
        /** @var User $user */
        $user = $this->getUser();
        $row = [];
        $image = null;
        if ($favorites = $user->getProfil()->getFavorite()->getValues()){
            foreach ($favorites as $favorite){

                /** @var User $favorite */
                if ($imgs = $favorite->getImg()->getValues()){
                    /** @var Img $img */
                    foreach ($imgs as $img){
                        if ($img->getIsProfile()){
                            $image = $img->getId();
                        }
                    }
                }
                    array_push($row, [
                        'id' => $favorite->getId(),
                        'alias' => $favorite->getPseudo(),
                        'img' => $image ? $image : null
                    ]);
                $image = null;
            }

            return $this->json($row);
        }

        return $this->json($row);
    }
}
