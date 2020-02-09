<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationController extends AbstractController
{
    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/trans/newmessage", name="api_trans_new_message", methods={"GET"})
     */
    public function getMessageTrans(Request $request, TranslatorInterface $translator){
        return $this->json(['trans' => $translator->trans('new.message', [], null, $request->getLocale())]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/trans/notsub", name="api_trans_not_sub")
     */
    public function getRefusedTrans(Request $request, TranslatorInterface $translator){
        return $this->json(['trans' => $translator->trans('not.sub', [], null, $request->getLocale())]);
    }
}