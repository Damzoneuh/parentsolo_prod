<?php

namespace App\Controller;

use App\Entity\Diary;
use App\Entity\Img;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiaryController extends AbstractController
{
    private $_serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }
    /**
     * @Route("/diary", name="diary")
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
        return $this->render('diary/index.html.twig', [
            'data' => $data,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     * @Route("/api/diary/all", name="api_diary_all", methods={"GET"})
     */
    public function getAllDiary(Request $request){
        $diaries = $this->getDoctrine()->getRepository(Diary::class)->findBy(['isValidate' => true]);
        $data = [];
        $date = new \DateTime('now');
        if ($diaries){
            foreach ($diaries as $diary){
                if ($diary->getDate()->getTimestamp() > $date->getTimestamp()){
                    array_push($data, [
                       'date' => $diary->getDate()->format('d-m-Y'),
                       'name' => $diary->getTitle(),
                       'location' => $diary->getLocation(),
                       'text' => $diary->getText(),
                       'id' => $diary->getId()
                    ]);
                }
            }
        }
        return $this->json($data);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     * @Route("/api/diary/add", name="api_diary_add", methods={"POST"})
     */
    public function addDiary(Request $request, TranslatorInterface $translator){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $em = $this->getDoctrine()->getManager();
        $diary = new Diary();
        $diary->setText($data['text']);
        $diary->setTitle($data['name']);
        $diary->setDate(new \DateTime($data['day'] . '-' . $data['month'] . '-' . $data['year']));
        $diary->setLocation($data['location']);
        $em->persist($diary);
        $em->flush();

        return $this->json($translator->trans('added content', [], null, $request->getLocale()));
    }
}
