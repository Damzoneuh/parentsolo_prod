<?php

namespace App\Controller;

use App\Entity\Img;
use App\Entity\Testimony;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;

class TestimonyController extends AbstractController
{
    private $_serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }
    /**
     * @Route("/testimony", name="testimony")
     */
    public function index()
    {
        /** @var $user User */
        $user = $this->getUser();
        $data = [];
        if ($user && $user->getImg()->count() > 0){
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
        return $this->render('testimony/index.html.twig', [
            'data' => $data,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/testimony/all", name="api_testimony_all", methods={"GET"})
     */
    public function getAllTestimony(){
        $testimonies = $this->getDoctrine()->getRepository(Testimony::class)->getActualAndValidate();
        $data = [];
        if ($testimonies){
            foreach ($testimonies as $testimony){
                $img = $this->getDoctrine()->getRepository(Img::class)->findOneBy(['isProfile' => true, 'user' => $testimony->getUser()->getId()]);
                array_push($data, [
                    'alias' => $testimony->getUser()->getPseudo(),
                    'img' => $img ? $img->getId() : null,
                    'title' => $testimony->getTitle(),
                    'text' => $testimony->getText(),
                    'isMan' => $testimony->getUser()->getProfil()->getIsMan()
                ]);
            }
        }

        return $this->json($data, 200);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/testimony/add", name="api_testimony_add", methods={"POST"})
     */
    public function addTestimony(Request $request, TranslatorInterface $translator){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $testimony = new Testimony();
        $testimony->setTitle($data['title']);
        $testimony->setText($data['text']);
        $testimony->setUser($user);
        $em->persist($testimony);
        $em->flush();

        return $this->json($translator->trans('added content', [], null, $request->getLocale()), 200);
    }
}
