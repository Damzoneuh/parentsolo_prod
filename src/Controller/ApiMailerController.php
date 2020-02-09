<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\MailingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApiMailerController extends AbstractController
{
    private $_serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @param Request $request
     * @param MailingService $mailingService
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/messages/mailer", name="api_mailer", methods={"POST"})
     */
    public function index(Request $request, MailingService $mailingService)
    {
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $em = $this->getDoctrine()->getRepository(User::class);
        $target = $em->find($data['target']);
        $from = $em->find($data['from']);
        if ($target->getIsNotified() && !$target->getIsDeleted()){
            $mailingService->sendUnconnectedMail($from, $target);
        }
        return $this->json(['success' => 'ok']);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param MailingService $mailingService
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/mailer/notification", name="api_mailer_notification", methods={"POST"})
     */
    public function sendNotification(Request $request, TranslatorInterface $translator, MailingService $mailingService){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $user = $this->getDoctrine()->getRepository(User::class)->find($data['user']);
        $target = $this->getDoctrine()->getRepository(User::class)->find($data['target']);
        if ($user->getIsDeleted()){
            return $this->json(['error' => 'User deleted']);
        }
        if (!$user->getIsNotified()){
            return $this->json(['success' => 'ok']);
        }
        $content = null;
        $type = null;
        if ($data['type'] === 'flower'){
            $content = $translator->trans('flower.receive.message', [], null, strtolower($target->getLangForModeration()));
            $type = $translator->trans('new.notification', [], null, strtolower($target->getLangForModeration()));
        }
        else{
            $sender = $this->getDoctrine()->getRepository(User::class)->find($data['sender']);
            $content = $sender->getPseudo() . ' ' . $translator->trans('visit.receive', [], null, strtolower($target->getLangForModeration()));
        }
        $mailingService->sendNotification($target, $user, $type, $content, $data['type']);
        return $this->json(['success' => 'ok']);
    }
}
