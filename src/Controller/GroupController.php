<?php

namespace App\Controller;

use App\Entity\Groups;
use App\Entity\Img;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;

class GroupController extends AbstractController
{
    private $_serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/group", name="group")
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

        return $this->render('group/index.html.twig', [
            'data' => $data,
        ]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/group/trans", name="api_trans_group", methods={"GET"})
     */
    public function transGroup(Request $request, TranslatorInterface $translator){
        $data = [
            'group' => $translator->trans('groups', [], null, $request->getLocale()),
            'groupDescribe' => $translator->trans('groups.describe', [], null, $request->getLocale()),
            'showLink' => $translator->trans('groups.show.link', [], null, $request->getLocale()),
            'createLink' => $translator->trans('groups.create.link', [], null, $request->getLocale()),
            'createdBy' => $translator->trans('created.by', [], null, $request->getLocale()),
            'lastGroupLink' => $translator->trans('groups.last.link', [], null, $request->getLocale())
        ];

        return $this->json($data, 200);
    }



    /**
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/group/{id}", name="api_get_group", methods={"GET"})
     */
    public function getGroup($id = null){
        $em = $this->getDoctrine()->getRepository(Groups::class);
        /** @var User $user */
        $user = $this->getUser();
        if ($id){
            /** @var Groups $data */
            $data = $em->findOneBy(['id' => $id, 'isValidated' => true]);
            $row = [];
            if ($data){
                $img = null;
                if ($image = $this->getDoctrine()->getRepository(Img::class)->findOneBy(['groups' => $data])){
                    $img = $image->getId();
                }
                $isSub = false;
                $members = $data->getMembers()->getValues();
                /** @var User $member */
                foreach ($members as $member){
                    if ($member->getId() == $user->getId()){
                        $isSub = true;
                    }
                }
                $row = [
                    'createdBy' => $data->getCreatedBy(),
                    'img' => $img,
                    'id' => $data->getId(),
                    'description' => $data->getDescription(),
                    'members' => count($data->getMembers()->getValues()),
                    'isSub' => $isSub
                ];
            }
            return $this->json($row, 200);
        }
        $data = $em->findAll();
        $rows = [];
        if ($data){
            foreach ($data as $group){
                $isSub = false;
                $members = $group->getMembers()->getValues();
                /** @var User $member */
                foreach ($members as $member){
                    if ($member->getId() == $user->getId()){
                        $isSub = true;
                    }
                }
               if ($group->getIsValidated()){
                   $img = null;
                   if ($imgs = $this->getDoctrine()->getRepository(Img::class)->findOneBy(['groups' => $group])){
                       $img = $imgs->getId();
                   }
                   array_push($rows, [
                       'id' => $group->getId(),
                       'name' => $group->getName(),
                       'description' => $group->getDescription(),
                       'img' => $img,
                       'members' => count($group->getMembers()->getValues()),
                       'isSub' => $isSub
                   ]);
               }
            }
        }
        return $this->json($rows, 200);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/last/group", name="api_last_group", methods={"GET"})
     */
    public function getLastGroup(){
        $em = $this->getDoctrine();
        $groups = $em->getRepository(Groups::class)->getLastGroup();
        $data = [];
        /** @var Groups $group */
        foreach ($groups as $group){
            $createdBy = $this->getDoctrine()->getRepository(User::class)->find($group->getCreatedBy());
            $img = $em->getRepository(Img::class)->findOneBy(['groups' => $group]);
            $data['id'] = $group->getId();
            $data['name'] = $group->getName();
            $data['createdBy'] = $createdBy->getPseudo();
            $data['img'] = $img ? $img->getId() : '';
        }
        return $this->json($data, 200);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/group/create", name="admin_api_create_group", methods={"POST"})
     * @throws \Exception
     */
    public function createGroup(Request $request, TranslatorInterface $translator){
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $group = new Groups();
        $group->setCreatedBy($user->getId());
        $group->setName($request->get('name'));
        $group->setDescription($request->get('description'));
        $group->addMember($user);
        $em->persist($group);


        $file = $request->files->get('file');
        if ($ext = self::checkExtension($file)){
            $rand = self::genRandomName();

            $em = $this->getDoctrine()->getManager();
            $file->move($this->getParameter('storage.img') . '/', $rand . $ext);
            $img = new Img();
            $img->setPath($this->getParameter('storage.img') . '/' . $rand . $ext);
            $img->setTitle($request->get('name'));
            $img->setGroups($group);
            $img->setIsProfile(false);
            $em->persist($img);
            $em->flush();

            return $this->json($translator->trans('added content', [], null, $request->getLocale()), 200);
        }

        return $this->json($translator->trans('added content', [], null, $request->getLocale()));
    }


    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/admin/api/group/delete/{id}", name="admin_api_delete_group", methods={"DELETE"})
     */
    public function deleteGroup($id){
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository(Groups::class)->find($id);
        $em->remove($group);
        return $this->json('success');
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/get/group/user", name="api_get_group_user", methods={"GET"})
     */
    public function getUserGroup(Request $request, TranslatorInterface $translator){
        /** @var User $user */
        $user = $this->getUser();
        $groups = $user->getGroupsMembers()->getValues();
        $data = [];
        if (count($groups) > 0){
            foreach ($groups as $group) {
                $members = $group->getMembers()->getValues();
                if ($group->getIsValidated()) {
                    $img = null;
                    if ($imgs = $this->getDoctrine()->getRepository(Img::class)->findOneBy(['groups' => $group])) {
                        $img = $imgs->getId();
                    }
                    /** @var Groups $group */
                    $row = [
                        'createdBy' => $group->getCreatedBy(),
                        'img' => $img,
                        'id' => $group->getId(),
                        'name' => $group->getName(),
                        'description' => $group->getDescription(),
                        'members' => count($group->getMembers()->getValues()),
                        'isSub' => true
                    ];
                    array_push($data, $row);
                }
            }
        }
        return $this->json($data, 200);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/group/user", name="group_user_page")
     */
    public function getUserGroupPage(){
        /** @var $user User */
        $user = $this->getUser();
        //dump($user->getProfil()); die();
        $data = [];
        $img = $user->getImg()->getValues();
        if (!empty($img[0])){
            $image = $img[0];
            /** @var $image Img */
            $data['profilImg'] = $image->getId();
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
        return $this->render('group/user-group.html.twig', ['data' => $data]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/group/join", name="api_group_join", methods={"PUT"})
     */
    public function joinGroup(Request $request, TranslatorInterface $translator){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $group = $em->getRepository(Groups::class)->find($data['group']);
        $group->addMember($user);
        $em->flush();

        return $this->json($translator->trans('added content', [], null, $request->getLocale()), 200);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/group/leave", name="api_group_leave", methods={"PUT"})
     */
    public function leaveGroup(Request $request, TranslatorInterface $translator){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $group = $em->getRepository(Groups::class)->find($data['group']);
        $group->removeMember($user);
        $em->flush();

        return $this->json($translator->trans('removed content', [], null, $request->getLocale()), 200);
    }

    private function checkExtension(UploadedFile $file){
        switch ($file->getClientMimeType()){
            case 'image/png':
                return '.png';
                break;
            case 'image/gif':
                return '.gif';
            case 'image/jpeg':
                return '.jpeg';
            case 'image/jpg':
                return '.jpg';
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    private static function genRandomName() : string {
        return bin2hex(random_bytes(12));
    }
}
