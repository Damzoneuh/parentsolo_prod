<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactController extends AbstractController
{
    private $_serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }


    /**
     * @Route("/contact", name="contact")
     */
    public function index()
    {
        return $this->render('contact/index.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/contact/services", name="api_contact_get_services", methods={"GET"})
     */
    public function getService(){
        $services = [
            'subscribe',
            'press_contact',
            'add',
            'abuse_signal',
            'technical.issue',
            'Other'
        ];
        return $this->json($services, 200);
    }

    /**
     * @param \Swift_Mailer $mailer
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/contact/send", name="api_contact_send", methods={"POST"} )
     */
    public function sendAdminMail(\Swift_Mailer $mailer, Request $request, TranslatorInterface $translator){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $message = new \Swift_Message();
        $message->setTo('info@parentsolo.ch');
        $message->setFrom('contact@parentsolo.ch');
        $message->setSubject('Contact parentsolo');
        $message->setBody(
            $this->renderView('emails/admin.html.twig',
                [
                    'title' => $translator->trans($data['service'], [], null, 'fr'),
                    'message' => $data['message'],
                    'email' => $data['email']]
            ), 'text/html'
        );

        $mailer->send($message);

        return $this->json($translator->trans('your request has been sent', [], null, $request->getLocale()), 200);
    }
}
