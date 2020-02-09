<?php

namespace App\Controller;

use App\Entity\Flowers;
use App\Entity\Items;
use App\Entity\User;
use App\Service\ItemsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ItemsController extends AbstractController
{
    /**
     * @Route("/api/items/{id}", name="api_items", methods={"GET"})
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index($id = null)
    {
       $em = $this->getDoctrine()->getRepository(Items::class);
       if (!$id){
           $items = $em->findAll();
           $data = [];
           $itemList = [];
           foreach ($items as $item){
               $data['price'] = $item->getPrice();
               $data['type'] = $item->getType();
               $data['isSubscribe'] = $item->getIsASubscribe();
               $data['id'] = $item->getId();
               $data['duration'] = $item->getDuration();
               array_push($itemList, $data);
           }
           return $this->json($itemList);
       }
       $data = [];
       if (null === $item = $em->find($id)){
           return $this->json($data);
       }
       $data['type'] = $item->getType();
       $data['price'] = $item->getPrice();
       $data['isSubscribe'] = $item->getIsASubscribe();
       $data['duration'] = $item->getDuration();
       $data['id'] = $item->getId();
       return $this->json($data);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param ItemsService $itemsService
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/auth/flowers/{id}", name="api_auth_flowers", methods={"GET"})
     */
    public function checkFlowersAccess(Request $request, TranslatorInterface $translator, ItemsService $itemsService, $id = null){
        if (!$id){
            /** @var User $user */
            $user = $this->getUser();
        }

        else{
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        }

        if ($itemsService::checkFlowersRights($user) || in_array('ROLE_PREMIUM', $user->getRoles())){
            return $this->json([
                'success' => true, 'content' => $translator->trans('flower.success', [], null, $request->getLocale())
            ]);
        }
        return $this->json(false);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/flowers", name="api_flowers", methods={"GET"})
     */
    public function getAllFlowers(Request $request, TranslatorInterface $translator){
        $flowers = $this->getDoctrine()->getRepository(Flowers::class)->findAll();
        $data = [];
        foreach ($flowers as $flower){
            $data[$flower->getId()]['img'] = $flower->getImg()->getId();
            $data[$flower->getId()]['id'] = $flower->getId();
            $data[$flower->getId()]['type'] = $flower->getType();
            $data[$flower->getId()]['description'] = $translator->trans('flower ' . $flower->getType(), [], null, $request->getLocale());
        }

        return $this->json($data, 200);
    }
}
