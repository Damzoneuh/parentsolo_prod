<?php

namespace App\Controller;

use App\Entity\FlowerReceived;
use App\Entity\Img;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class FlowersController extends AbstractController
{
    /**
     * @Route("/flowers", name="flowers")
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
        return $this->render('flowers/index.html.twig', [
            'data' => $data,
        ]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/flower/received", name="api_get_flower_received", methods={"GET"})
     */
    public function getFlowerReceived(Request $request, TranslatorInterface $translator){
        /** @var User $user */
        $user = $this->getUser();
        $flowers = $this->getDoctrine()->getRepository(FlowerReceived::class)->findBy(['target' => $user]);
        $data = [];
        if (count($flowers) > 0){
            /** @var FlowerReceived $flower */
            foreach ($flowers as $flower){
                $profileImg = $flower->getSender()->getImg();
                $picture = null;
                if ($profileImg->count() > 0){
                    /** @var Img $img */
                    foreach ($profileImg->getValues() as $img){
                        if ($img->getIsProfile()){
                            $picture = $img->getId();
                        }
                    }
                }
                array_push($data, [
                    'id' => $flower->getId(),
                    'sender' => $flower->getSender()->getId(),
                    'message' => $flower->getMessage(),
                    'img' => $picture ? $picture : null,
                    'isMan' => $flower->getSender()->getProfil()->getIsMan(),
                    'alias' => $flower->getSender()->getPseudo(),
                    'flower' => [
                        'id' => $flower->getFlower()->getId(),
                        'type' => $flower->getFlower()->getType(),
                        'img' => $flower->getFlower()->getImg()->getId(),
                        'description' => $translator->trans('flower ' . $flower->getFlower()->getType(), [], null, $request->getLocale())                    ]
                ]);
            }
            return $this->json($data, 200);
        }
        return $this->json($data, 200);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/flower/read", name="api_flower_read", methods={"PUT"})
     */
    public function markFlowersRead(){
        $user = $this->getUser();
        $flowers = $this->getDoctrine()->getRepository(FlowerReceived::class)->findBy(['target' => $user]);
        $em = $this->getDoctrine()->getManager();
        if (count($flowers) > 0){
            foreach ($flowers as $flower){
                $flower->setIsRead(true);
                $em->flush();
            }
        }
        return $this->json(true, 200);
    }


    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/flower/access/{id}", name="aapi_flower_access", methods={"GET"})
     */
    public function getFlowerAccess($id){
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        if (in_array('ROLE_PREMIUM', $user->getRoles()) || in_array('ROLE_ADMIN', $user->getRoles())){
            return $this->json(true);
        }
        if (in_array('ROLE_MEDIUM', $user->getRoles()) || in_array('ROLE_BASIC', $user->getRoles()) && $user->getFlowerNumber() && $user->getFlowerNumber() > 0){
            return $this->json(true);
        }
        return $this->json(false);
    }
}
