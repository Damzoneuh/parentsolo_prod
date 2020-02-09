<?php

namespace App\Controller;

use App\Entity\Img;
use App\Entity\User;
use App\Entity\Visit;
use App\Mailer\Mailing;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;

class VisitController extends AbstractController
{
    private $_serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/visit", name="visit")
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
        return $this->render('visit/index.html.twig', [
            'data' => $data,
        ]);
    }

    /**
     * @param Request $request
     * @param Mailing $mailing
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     * @Route("/api/set/visit", name="api_set_visit", methods={"POST"})
     */
    public function setVisit(Request $request, Mailing $mailing, TranslatorInterface $translator){
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $data = $this->_serializer->decode($request->getContent(), 'json');
        if ($visits = $em->getRepository(User::class)->find($data['target'])->getVisits()){
            if ($visits->count() > 0){
                foreach ($visits as $visit){
                    if ($visit->getVisitor() === $user){
                        $visit->setDate(new \DateTime('now'));
                        return $this->json(true, 200);
                    }
                }
            }
        }
        $target = $em->getRepository(User::class)->find($data['target']);
        $visit = new Visit();
        $visit->setVisitor($user);
        $visit->setDate(new \DateTime('now'));
        $em->persist($visit);
        $target->addVisit($visit);
        $em->flush();

        if ($target->getIsNotified()){
            $mailing->sendNotification($target, $user, 'visit',
                $translator->trans('new visit', [], null, $request->getLocale()),
                $user->getPseudo() . ' ' . $translator->trans('as visit your profile', [], null, $request->getLocale())
                );
        }
        return $this->json(true, 200);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/visit", name="api_visit", methods={"GET"})
     * @throws \Exception
     */
    public function getVisit(){
        /** @var User $user */
        $user = $this->getUser();
        $data = [];
        $visits = $user->getVisits()->count() > 0 ? $user->getVisits()->getValues() : null;
        if($visits){
            /** @var Visit $visit */
            foreach ($visits as $visit){
                $visitor = $visit->getVisitor();
                $img = $this->getDoctrine()->getRepository(Img::class)->findOneBy(['isProfile' => true, 'user' => $visitor]);
                array_push($data, [
                    'id' => $visitor->getId(),
                    'alias' => $visitor->getPseudo(),
                    'img' => $img ? $img->getId() : null,
                    'city' => $visitor->getProfil()->getCity()->getName(),
                    'canton' => $visitor->getProfil()->getCity()->getCanton()->getName(),
                    'date' => $visit->getDate()->format('d/m/Y'),
                    'age' => self::getAge($visit->getVisitor()->getBirthdate()),
                    'isMan' => $visit->getVisitor()->getProfil()->getIsMan()
                ]);
            }
        }
        return $this->json($data, 200);
    }

    private static function getAge($birthDate){
        $date = new \DateTime('now');
        $age = $date->diff($birthDate);
        return $age->y;
    }
}
