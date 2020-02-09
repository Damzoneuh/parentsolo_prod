<?php

namespace App\Controller;

use App\Async\RenewSub;
use App\Entity\Groups;
use App\Entity\Items;
use App\Entity\Payment;
use App\Entity\PaymentProfil;
use App\Entity\Subscribe;
use App\Entity\Testimony;
use App\Entity\User;
use App\Service\ItemsService;
use App\Service\MailingService;
use App\Service\PostFinanceService;
use App\Service\SubscribeService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;


class PaymentController extends AbstractController
{
    private $_serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @param int $itemId
     * @param Session $session
     * @param TranslatorInterface $translator
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/payment/{itemId}", name="payment")
     */
    public function index(int $itemId, Session $session, TranslatorInterface $translator, Request $request)
    {
       $session->set('itemId', $itemId);
        $form = $this->createFormBuilder()
            ->add('BRAND', ChoiceType::class, [
                'choices' => [
                    'Visa' => 'VISA',
                    'American Express' => 'American Express',
                    'MasterCard' => 'MasterCard',
                    'JCB' => 'JCB'
                ],
                'label' => $translator->trans('brand', [], null, $request->getLocale())
            ])
            ->add('submit', SubmitType::class, [
                'label' => $translator->trans('validate', [], null, $request->getLocale())
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $session->set('brand', $data['BRAND']);
            return $this->redirectToRoute('payment_credentials');
        }

        return $this->render('payment/index.html.twig', ['form' => $form->createView(), 'item' => $itemId]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param Session $session
     * @Route("/credentials", name="payment_credentials")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendWithBrand(Request $request, TranslatorInterface $translator, Session $session){
        $item = $this->getDoctrine()->getRepository(Items::class)->find($session->get('itemId'));
        $sha = $this->getParameter('sha');
        $sign = sha1(
            'ACCEPTURL='.$this->getParameter('app.url').'/accept'.
            //$sha.'ALIASPERSISTEDAFTERUSE=Y'.
            $sha.'BRAND='.$session->get('brand') .
            $sha.'EXCEPTIONURL='.$this->getParameter('app.url').'/cancel'.
            $sha.'PSPID='.$this->getParameter('pspid').
            $sha);
        $body = [
            'ACCEPTURL' => $this->getParameter('app.url').'/accept',
            //'ALIASPERSISTEDAFTERUSE' => 'Y',
            'EXCEPTIONURL' => $this->getParameter('app.url').'/cancel',
            'SHASIGN' => $sign,
            'PSPID' => $this->getParameter('pspid'),
            'BRAND' => $session->get('brand'),
            'URI' => $this->getParameter('postfinance.tokenisation')
        ];
        $data['amount'] = $item->getPrice();
        $data['context'] = $item->getType();
        $data['user'] = $this->getUser()->getEmail();
        $data['itemId'] = $item->getId();
        $data['currency'] = 'CHF';
        $data['sub'] = $item->getIsASubscribe() ? $translator->trans('subscribe', [], null, $request->getLocale()) : '';
        $data['trans'] = [
            'sub' => $translator->trans('subscribe', [], null, $request->getLocale()),
            'amount' => $translator->trans('amount', [], null, $request->getLocale()),
            'product' => $translator->trans('product', [], null, $request->getLocale()),
            'validate' => $translator->trans('validate', [], null, $request->getLocale())
        ];
        return $this->render('payment/credentials.html.twig', ['settings' => $body, 'data' => $data]);
    }

    /**
     * @param Request $request
     * @param Session $session
     * @param TranslatorInterface $translator
     * @param SubscribeService $subscribeService
     * @param ItemsService $itemsService
     * @param PostFinanceService $postFinanceService
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @Route("/accept", name="accept")
     */
    public function accept(Request $request, Session $session, TranslatorInterface $translator, SubscribeService $subscribeService, ItemsService $itemsService, PostFinanceService $postFinanceService){
        $item = $this->getDoctrine()->getRepository(Items::class)->find($session->get('itemId'));
        $data = $request->query->all();
        /** @var User $user */
        $user = $this->getUser();
        if ($item->getIsASubscribe()){
            if ($data['Alias_StorePermanently'] == 'N'){
                //dump('ici');die();
                $this->addFlash('error', $translator->trans('check.store', [], null, $request->getLocale()));
                return $this->redirectToRoute('shop');
            }

            $res = $postFinanceService->createPayment($data['Alias_OrderId'], $data['Alias_AliasId'], $item->getPrice(), $session->get('brand'));
            if ($res && $res->getStatusCode() == 200 && $res->getContent()){
                $sub = $this->_serializer->decode($res->getContent(), 'xml');
                if ($sub['@STATUS'] == 5 || $sub['@STATUS'] == 9){
                    try {
                        $em = $this->getDoctrine()->getManager();
                        $subscribeService->setSixSubscription($user, $item, $data['Alias_AliasId']);
                        $paymentProfile = new PaymentProfil();
                        $paymentProfile->setSelected(true);
                        $paymentProfile->setUser($user);
                        $paymentProfile->setExpYear(00);
                        $paymentProfile->setExpMonth(00);
                        $paymentProfile->setAlias($data['Alias_AliasId']);
                        $paymentProfile->setCardName($session->get('brand'));
                        $paymentProfile->setDisplayText($data['Card_CardNumber']);

                        $em->persist($paymentProfile);
                        $user->addPaymentProfil($paymentProfile);
                        $em->flush();
                    } catch (OptimisticLockException $e) {
                        $this->addFlash('error', $translator->trans('payment.refused', [], null, $request->getLocale()));
                        return $this->redirectToRoute('shop');
                    } catch (ORMException $e) {
                        $this->addFlash('error', $translator->trans('payment.refused', [], null, $request->getLocale()));
                        return $this->redirectToRoute('shop');
                    }
                }
                //TODO timeout html -> logout
                return $this->render('payment/redirect.html.twig');
            }
            $this->addFlash('error', $translator->trans('payment.refused', [], null, $request->getLocale()));
            return $this->redirectToRoute('payment', ['itemId' => $item->getId()]);
        }
        $res = $postFinanceService->createPayment($data['Alias_OrderId'], $data['Alias_AliasId'], $item->getPrice(), $session->get('brand'));
        if($res && $res->getStatusCode() == 200){
            $product = $this->_serializer->decode($res->getContent(), 'xml');
            if ($product['@STATUS'] == 5 || $product['@STATUS'] == 9){
                $em = $this->getDoctrine()->getManager();
                $payment = new Payment();
                $payment->setUser($user);
                $payment->setUniqKey($data['Alias_OrderId']);
                $payment->setIsCaptured(true);
                $payment->setMethod('six');
                $payment->setDate(new \DateTime('now'));
                $payment->setIsAccepted(true);
                $em->persist($payment);
                $em->flush();
                $itemsService->createItem($item->getId(), $this->getUser());
                $this->addFlash('success', $translator->trans('payment.success', [], null, $request->getLocale()));
                return $this->redirectToRoute('shop');
            }
            $this->addFlash('error', $translator->trans('payment.refused', [], null, $request->getLocale()));
            return $this->redirectToRoute('shop');
        }
        $this->addFlash('error', $translator->trans('payment.refused', [], null, $request->getLocale()));
        return $this->redirectToRoute('shop');
    }

//    /**
//     * @param Request $request
//     * @param Session $session
//     * @param ItemsService $itemsService
//     * @param SubscribeService $subscribeService
//     * @return \Symfony\Component\HttpFoundation\JsonResponse
//     * @throws \Exception
//     * @Route("/api/card", name="payment_card_credentials", methods={"POST"})
//     */
//    public function getCardCredentials(Request $request, Session $session, ItemsService $itemsService, SubscribeService $subscribeService){
//        if (!$request->isMethod('POST') || !self::checkToken($request)){
//            throw new AccessDeniedException();
//        }
//        $content = $this->_serializer->decode($request->getContent(), 'json');
//        $data['credentials'] = $content['credentials'];
//        $data['amount'] = $content['settings']['amount'];
//        $data['context'] = $content['settings']['context'];
//        $data['currency'] = $content['settings']['currency'];
//        $six = self::createSixInstance();
//        $payment = json_decode($six->createDirectPayment($data));
//        /** @var User $user */
//        $user = $this->getUser();
//        $em = $this->getDoctrine()->getManager();
//
//        $pay = new PaymentProfil();
//        $pay->setUser($user);
//        $pay->setAlias($payment->alias);
//        $pay->setCardName($payment->PaymentMeans->Brand->Name);
//        $pay->setDisplayText($payment->PaymentMeans->DisplayText);
//        $pay->setExpMonth($payment->PaymentMeans->Card->ExpMonth);
//        $pay->setExpYear($payment->PaymentMeans->Card->ExpYear);
//        if (!$user->getPaymentProfil()){
//            $pay->setSelected(true);
//        }
//        else{
//            foreach ($user->getPaymentProfil() as $card){
//                $card->setSelected(false);
//            }
//            $pay->setSelected(true);
//        }
//        $em->persist($pay);
//        $item = $em->getRepository(Items::class)->find($session->get('itemId'));
//        if ($item->getIsASubscribe()){
//            if($subscribeService->setSixSubscription($user, $item->getId(), $payment->alias)){
//                return $this->json(['success' => 'You will be logout to activate your subscription']);
//            }
//            return $this->json(['error' => 'An error as been throw during your payment']);
//        }
//        $paid = new Payment();
//        $paid->setPaymentProfil($pay);
//        $paid->setUniqKey($payment->Transaction->Id);
//        $paid->setMethod('six');
//        $paid->setIsCaptured(false);
//        $paid->setUser($user);
//        $paid->setDate(new \DateTime('now'));
//        $paid->addItem($item);
//        $paid->setIsAccepted(true);
//        $em->persist($paid);
//        $em->flush();
//
//        $itemsService->createItem($item->getId(), $user->getId());
//        $session->remove('itemId');
//        return $this->json($this->_serializer->encode($payment, 'json'));
//    }

//    /**
//     * @param Request $request
//     * @param Session $session
//     * @param ItemsService $itemsService
//     * @param SubscribeService $subscribeService
//     * @return \Symfony\Component\HttpFoundation\JsonResponse
//     * @throws \Exception
//     * @Route("/api/payment/knowcard", name="api_know_card", methods={"POST"})
//     */
//    public function payWithAlias(Request $request, Session $session, ItemsService $itemsService, SubscribeService $subscribeService){
//        if (!self::checkToken($request) || !$request->isMethod('POST')){
//            throw new AccessDeniedException();
//        }
//        $em = $this->getDoctrine()->getManager();
//        $data = $this->_serializer->decode($request->getContent(), 'json');
//        /** @var User $user */
//        $user = $this->getUser();
//        $cards = $user->getPaymentProfil();
//        $usedCard = $em->getRepository(PaymentProfil::class)->findOneBy(['alias' => $data['alias']]);
//        foreach ($cards as $card){
//            if ($usedCard->getId() !== $card->getId() && $card->getSelected() === true){
//                $card->setSelected(false);
//                $em->persist($card);
//                $usedCard->setSelected(true);
//                $em->persist($usedCard);
//                $em->flush();
//            }
//        }
//        $six = self::createSixInstance();
//        $response = json_decode($six->createAliasPayment($data['alias'], $data['settings']['amount'], $data['settings']['context']));
//        $paid = new Payment();
//        $item = $em->getRepository(Items::class)->find($session->get('itemId'));
//        if ($item->getIsASubscribe()){
//            if($subscribeService->setSixSubscription($user, $item->getId(), $usedCard->getAlias())){
//                return $this->json(['success' => 'You will be logout to activate your subscription']);
//            }
//            return $this->json(['error' => 'An error as been throw during your payment']);
//        }
//        $paid->setPaymentProfil($usedCard);
//        $paid->setUniqKey($response->Transaction->Id);
//        $paid->setMethod('six');
//        $paid->setIsCaptured(false);
//        $paid->setDate(new \DateTime('now'));
//        $paid->setIsAccepted(true);
//        $paid->setUser($user);
//        $paid->addItem($item);
//
//        $em->persist($paid);
//        $em->flush();
//
//        $itemsService->createItem($item->getId(), $user->getId());
//        $session->remove('itemId');
//        return $this->json(json_encode($response));
//    }
//
//    /**
//     * @return \Symfony\Component\HttpFoundation\JsonResponse
//     * @Route("/api/payment/profil", name="api_know_card_profil", methods={"GET"})
//     */
//    public function checkIfKnowCard(){
//        /** @var User $user */
//        $user = $this->getUser();
//        if(count($user->getPaymentProfil()) > 0){
//            $cards = [];
//            foreach ($user->getPaymentProfil() as $paymentProfil){
//                $card['expMount'] = $paymentProfil->getExpMonth();
//                $card['expYear'] = $paymentProfil->getExpYear();
//                $card['alias'] = $paymentProfil->getAlias();
//                $card['displayText'] = $paymentProfil->getDisplayText();
//                $card['cardName'] = $paymentProfil->getCardName();
//                $card['id'] = $paymentProfil->getId();
//                array_push($cards,$card);
//            }
//            return $this->json($cards);
//        }
//        return $this->json([]);
//    }
//
//    private function createSixInstance(){
//        return new SixPayment($this->getParameter('api.six.customer'),
//            $this->getParameter('api.six.terminal'),
//            rand(1, 100),
//            $this->getParameter('api.six.key'),
//            $this->getParameter('api.six.uri'));
//    }
//
//    private function checkToken(Request $request){
//        $data = $this->_serializer->decode($request->getContent(), 'json');
//        $submittedToken = $data['token'];
//        if($this->isCsrfTokenValid('payment', $submittedToken)){
//            return true;
//        }
//        return false;
//    }
//
//    /**
//     * @return \Symfony\Component\HttpFoundation\JsonResponse
//     * @Route("/api/cron/capture", name="api_cron_capture")
//     */
//    public function doACapture(){
//        SixProcess::capture();
//        return $this->json('ok');
//    }

    /**
     * @Route("/cancel", name="cancel")
     * @param Session $session
     * @return RedirectResponse
     */
    public function cancel(Session $session){
        if ($session->get('itemId')){
            $this->addFlash('error', 'transaction annulée');
            return $this->redirectToRoute('shop');
        }
        return $this->redirectToRoute('shop');
    }

    /**
     * @param $token
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/cron/renew/{token}", name="cron_renew", methods={"GET"})
     */
    public function renewSub($token){
        if ($token == $this->getParameter('node.token')){
            RenewSub::renewSub();
            return $this->json('renew cron done');
        }
        return $this->json('bad token');
    }
}
//Alias.AliasId=&Card.Bin=&Card.Brand=VISA&Card.CardNumber=&Card.CardHolderName=&Card.Cvc=&Card.ExpiryDate=&Alias.NCError=55555555&Alias.NCErrorCardNo=0&Alias.NCErrorCN=60001057&Alias.NCErrorCVC=0&Alias.NCErrorED=0&Alias.OrderId=&Alias.Status=1&Alias.StorePermanently=Y&SHASign=E43A6BB747E246EDA5C38BF0485DE5355B7E2E86