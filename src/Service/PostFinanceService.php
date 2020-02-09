<?php


namespace App\Service;

use App\Entity\Payment;
use App\Entity\PaymentProfil;
use App\Entity\Subscribe;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PostFinanceService
{
    private $_em;
    private $_serializer;
    private $_uri;
    private $_pspId;
    private $_userId;
    private $_pswd;
    private $_sha;

    public function __construct(EntityManager $entityManager, \Swift_Mailer $mailer, \Twig_Environment $twig_Environment, ParameterBagInterface $parameterBag)
    {
        $this->_em = $entityManager;
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
        $this->_uri = $parameterBag->get('postfinance.directlink');
        $this->_pswd = $parameterBag->get('pswd');
        $this->_userId = $parameterBag->get('userid');
        $this->_pspId = $parameterBag->get('pspid');
        $this->_sha = $parameterBag->get('sha');
    }

   public function createPayment($orderId, $alias, $amount, $brand){
       $client = HttpClient::create();
       $sha = $this->_sha;
       $sign =
           'ALIAS='.$alias.$sha.
           'AMOUNT='.$amount * 100 .$sha.
           'BRAND='.$brand.$sha.
           'CURRENCY=CHF'.$sha.
           'OPERATION=SAL'.$sha.
           'ORDERID='.$orderId.$sha.
           'PSPID='.$this->_pspId.$sha.
           'PSWD='.$this->_pswd.$sha.
           'USERID='.$this->_userId.$sha
       ;
       $body = [
           'PSPID' => $this->_pspId,
           'SHASIGN' => sha1($sign),
           'AMOUNT' => $amount * 100,
           'ALIAS' => $alias,
           'CURRENCY' => 'CHF',
           'ORDERID' => $orderId,
           'OPERATION' => 'SAL',
           'USERID' => $this->_userId,
           'PSWD' => $this->_pswd,
           'BRAND' => $brand
       ];
       try {
           //dump($sign); die();
           return $client->request('POST', $this->_uri, [
               'body' => $body
           ]);
       } catch (TransportExceptionInterface $e) {
           return false;
       }
   }

   public function renew() : void {
        $em = $this->_em;
        $renews = $em->getRepository(User::class)->getRenewableUsers();
        if (count($renews) > 0){
            /** @var User $renew */
            foreach ($renews as $renew){
                $sub = $em->getRepository(Subscribe::class)->find($renew->getSubscribe());
                if ($sub->getPlan()){
                    if (!strstr($sub->getPlan(),'I-')){
                        $order = rand(1000, 99999999999999999);
                        $paymentProfile = $renew->getPaymentProfil();
                        $card = null;
                        if ($paymentProfile->count() > 0){
                            /** @var PaymentProfil $p */
                            foreach ($paymentProfile->getValues() as $p){
                                if ($p->getSelected()){
                                    $card = $p;
                                }
                            }
                        }
                        if ($card){
                            if (self::isDeadline($renew) || null === self::isDeadline($renew)){
                                $res = self::createPayment($order, $card->getAlias(), $sub->getItem()->getPrice(), $card->getCardName());
                                if ($res && $res->getStatusCode() == 200){
                                    $data = $this->_serializer->decode($res->getContent(), 'xml');
                                    if ($data['@STATUS'] == 5 || $data['@STATUS'] == 9 ){
                                        $sub->setDeadline(new \DateTime('+' . $sub->getItem()->getDuration() . 'month'));
                                        $payment = new Payment();
                                        $payment->setUser($renew);
                                        $payment->setPaymentProfil($paymentProfile);
                                        $payment->setIsAccepted(true);
                                        $payment->setIsCaptured(true);
                                        $payment->setDate(new \DateTime('now'));
                                        $payment->setMethod('six');
                                        $payment->addItem($sub->getItem());
                                        $em->persist($payment);
                                        $em->flush();

                                    }
                                    $sub->setIsAuthorized(false);
                                    $em->flush();
                                }
                            }
                        }
                    }
                }
            }
        }
   }

   private function isDeadline(User $user){
        $now = new \DateTime('now');
        $firstCall = strtotime('-5 day', $now);
        $lastCall  = $now->getTimestamp();
        if ($user->getSubscribe()->getDeadline()->getTimestamp() > $firstCall && $user->getSubscribe()->getDeadline()->getTimestamp() < $lastCall){
            if ($user->getSubscribe()->getDeadline()->getTimestamp() > $firstCall && $user->getSubscribe()->getDeadline()->getTimestamp() < strtotime('-4 day', $now)){
                return null;
            }
            if ($user->getSubscribe()->getIsAuthorized()){
                return false;
            }
            return true;
        }
       return false;
   }
}