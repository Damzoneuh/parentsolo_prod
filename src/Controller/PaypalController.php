<?php

namespace App\Controller;

use App\Async\CreateItem;
use App\Entity\Items;
use App\Entity\Payment;
use App\Entity\Subscribe;
use App\Entity\User;
use App\Service\ItemsService;
use App\Service\SubscribeService;
use backndev\paypal\PayPal;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PaypalController extends AbstractController
{
    private $_serializer;
    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }


    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @Route("/paypal/{id}", name="paypal")
     */
    public function index($id){
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Items::class)->find($id);
        $data = [
            'client' => $this->getParameter('api.pp.client'),
            'price' => $item->getPrice(),
            'subscribe' => $item->getIsASubscribe(),
            'id' => $id
        ];
        if (true === $item->getIsASubscribe()){
//            $paypal = self::createPaypalInstance();
//            $plan = $this->_serializer->decode($paypal->setSubscription($item), 'json');
            $data['plan'] = $item->getPaypalProduct();
            //TODO remettre le choix au moment du payment au shop
        }
        return $this->render('paypal/index.html.twig', $data);
    }

    /**
     * @param Request $request
     * @param ItemsService $itemsService
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @Route("api/paypal/complete", name="api_paypal", methods={"POST"})
     */
    public function setDirectOrder(Request $request, ItemsService $itemsService)
    {
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $paypal = self::createPaypalInstance();
        $capture = $this->_serializer->decode($paypal->setCapture($data['details']['id']), 'json');
        if($data['details']['status'] == 'COMPLETED' && $data['details']['intent'] == 'CAPTURE') {
            $em = $this->getDoctrine()->getManager();
            $item = $em->getRepository(Items::class)->find($data['item']);
            /** @var User $user */
            $user = $this->getUser();
            CreateItem::createItem($user->getId(), $data['item']);
            $payment = new Payment();
            $payment->setIsCaptured(true);
            $payment->setUniqKey($data['details']['id']);
            $payment->setMethod('paypal');
            $payment->setUser($user);
            $payment->setDate(new \DateTime('now'));
            $payment->setIsAccepted(true);
            $payment->addItem($item);
            $em->persist($payment);
            $em->flush();
            $itemsService->createItem($item->getId(), $user->getId());
            return $this->json($capture);
        }
        return $this->json(['error' => 'payment refused']);
    }

    /**
     * @param Request $request
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @Route("/api/paypal/approuve/sub", name="api_approuve_sub")
     */
    public function approuveSubscribe(Request $request, Session $session){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $item = $this->getDoctrine()->getRepository(Items::class)->find($data['item_id']);
        $sub = self::createPaypalInstance();
        $session->set('item', $data['item_id']);
        $response = $sub->approuveSubscription($item, $this->getUser(), $data['plan_id']);
        return $this->json($response);
    }

    /**
     * @param Request $request
     * @param Session $session
     * @param SubscribeService $service
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/paypal/accept/sub", name="paypal_accept_sub")
     */
    public function acceptSubscribe(Request $request, Session $session, SubscribeService $service){
        $sub = $request->get('subscription_id');
        if ($service->setPayPalSubscribe($this->getUser(),$sub, $session->get('item'))){
            return $this->redirectToRoute('app_logout');
        }
        return $this->redirectToRoute('home');
    }

    private function checkToken(Request $request){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $submittedToken = $data['token'];
        if($this->isCsrfTokenValid('payment', $submittedToken)){
            return true;
        }
        return false;
    }

    /**
     * @return PayPal
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function createPaypalInstance(){
        return new PayPal($this->getParameter('api.pp.client'), $this->getParameter('api.pp.secret'), $this->getParameter('api.pp.uri'));
    }
}
