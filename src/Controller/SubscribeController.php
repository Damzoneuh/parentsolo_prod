<?php


namespace App\Controller;


use App\Entity\Items;
use App\Entity\Subscribe;
use App\Entity\User;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SubscribeController extends AbstractController
{
    private $_serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/api/subscribe", name="api_subscribe")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function setSubscription(Request $request){
        if (!self::checkToken($request)){
            throw new AccessDeniedException('invalid token');
        }
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Items::class)->find($data['id']);
        $now = new \DateTime('now', new DateTimeZone('Europe/Paris'));
        $month = (int)$now->format('m') + (int)$item->getDuration();
        $year = (int)$now->format('Y');
        if ($month > 12){
            $month = $month - 12;
            $year = $year + 1;
        }
        $deadline = new \DateTime($year . '-' . $month . '-' . $now->format('d'), new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        /** @var User $user */
        $subscribe = new Subscribe();
        $subscribe->setDeadline($deadline);
        $subscribe->setItem($item);
        $subscribe->setIsAuthorized(true);
        $em->persist($subscribe);
        $user->setSubscribe($subscribe);
        $user->setRoles(['ROLE_' . $item->getRole(), 'ROLE_USER']);
        $em->flush();

        return $this->json($user->getSubscribe()->getId());
    }


    private function checkToken(Request $request){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $submittedToken = $data['token'];
        if($this->isCsrfTokenValid('payment', $submittedToken)){
            return true;
        }
        return false;
    }
}