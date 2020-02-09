<?php

namespace App\Controller;

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

class ImageController extends AbstractController
{
    private $_serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/api/image", name="api_image")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function receiveUserImg(Request $request, TranslatorInterface $translator)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        if ($ext = self::checkExtension($file)){
            $rand = self::genRandomName();
            $em = $this->getDoctrine()->getManager();
            $file->move($this->getParameter('storage.img') . '/', $rand . $ext);
            $img = new Img();
            $img->setPath($this->getParameter('storage.img') . '/' . $rand . $ext);
            $img->setTitle($request->get('name'));
            if ($request->get('is_profile')){
                if ($user->getImg()->count() > 0){
                    $imgs = $user->getImg()->getValues();
                    foreach ($imgs as $actualImg){
                        $actualImg->setIsProfile(false);
                        $em->flush();
                    }
                }
                $img->setIsProfile(true);
            }
            else{
                $img->setIsProfile(false);
            }
            $em->persist($img);
            //dump($user); die();
            $user->addImg($img);
            $em->flush();
            return $this->json(['success' => $translator->trans('img.added', [], null, $request->getLocale())]);
        }
        else{
            return $this->json(['error' => $translator->trans('bad.img', [], null, $request->getLocale())]);
        }
    }

    /**
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/img/{id}", name="api_get_img", methods={"GET"})
     */
    public function getImg($id = null){
        if (!$id){
            $array = [];
            /** @var User $user */
            $user = $this->getUser();
            $imgs = $user->getImg()->getValues();
            foreach ($imgs as $img){
                /** @var Img $img */
                if($img->getIsValidated()){
                    $group = $img->getGroups();
                    $child = $img->getChilds();
                    $content['path'] = $img->getPath();
                    $content['title'] = $img->getTitle();
                    $content['id'] = $img->getId();
                    $content['isProfile'] = $img->getIsProfile();
                    $content['isChild'] = $group ? $group->getId() : null;
                    $content['isGroup'] = $child ? $child->getId() : null;
                    array_push($array, $content);
                }
            }
            return $this->json($array);
        }
        $img = $this->getDoctrine()->getRepository(Img::class)->findOneBy(['id' => $id, 'isValidated' => true]);
        $content['id'] = $img->getId();
        $content['title'] = $img->getTitle();
        $content['path'] = $img->getPath();
        return $this->json($img);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/img/setasprofile", name="api_set_as_profile", methods={"PUT"})
     */
    public function setAsProfile(Request $request, TranslatorInterface $translator){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $id = $data['img'];
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if ($user->getImg()->count() > 0){
            $imgs = $user->getImg()->getValues();
            /** @var Img $img */
            foreach ($imgs as $img){
                if ($img->getId() === $id){
                    $img->setIsProfile($img->getIsProfile() ? false : true);
                    $em->flush();
                }
                else{
                    $img->setIsProfile(false);
                    $em->flush();
                }
            }
        }
        return $this->json($translator->trans('added content', [], null, $request->getLocale()));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @Route("/api/img/render/{id}", name="api_render_img", methods={"GET"})
     */
    public function renderImg($id){
        $image = $this->getDoctrine()->getRepository(Img::class)->find($id);
        return $this->file($image->getPath());
    }

    /**
     * @param $name
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @Route("/api/asset/{name}", name="api_asset_img")
     */
    public function renderAssetsImage($name){
        return $this->file('/var/www/html/public/img/' . $name );
    }

    private function checkExtension(UploadedFile $file){
        switch ($file->getClientMimeType()){
            case 'image/png' || 'application/octet-stream':
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
