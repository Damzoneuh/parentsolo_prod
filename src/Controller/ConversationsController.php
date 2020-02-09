<?php

namespace App\Controller;

use App\Entity\Img;
use App\Entity\Messages;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConversationsController extends AbstractController
{
    /**
     * @Route("/conversations", name="conversations")
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
        return $this->render('conversations/index.html.twig', [
            'data' => $data,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/conversation", name="api_conversation", methods={"GET"})
     */
    public function getConversation(){
        /** @var User $user */
        $user = $this->getUser();
        $messages = $this->getDoctrine()->getRepository(Messages::class)->findMyMessages($user->getId());
        $data = [];
        if ($messages){
            foreach ($messages as $message){
                if ($message->getMessageFrom() != $user->getId() || $message->getMessageTo() != $user->getId()){
                    if (!isset($data[$message->getMessageFrom() != $user->getId() ? $message->getMessageFrom() : $message->getMessageTo()])){
                        $data[$message->getMessageFrom() != $user->getId() ? $message->getMessageFrom() : $message->getMessageTo()] = [];
                    }
                   array_push( $data[$message->getMessageFrom() != $user->getId() ? $message->getMessageFrom() : $message->getMessageTo()], [
                       'content' => $message->getContent(),
                       'from' => $message->getMessageFrom(),
                       'to' => $message->getMessageTo(),
                       'isRead' => $message->getIsRead(),
                       'id' => $message->getId()
                   ]);
                }
            }
        }
        return $this->json($data, 200);
    }
}
