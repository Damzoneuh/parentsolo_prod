<?php

namespace App\Controller;

use App\Entity\Cities;
use App\Entity\User;
use App\Service\SearchService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchController extends AbstractController
{
    private $_serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/api/search", name="api_search")
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param SearchService $searchService
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function search(Request $request, TranslatorInterface $translator, SearchService $searchService){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $citiesRepository = $this->getDoctrine()->getRepository(Cities::class);
        $search = [];
        foreach ($userRepository->findBy(['isValidated' => true, 'isConfirmed' => true, 'isDeleted' => false], ['id' => 'ASC']) as $user){
            if (null !== $user->getProfil()->getRelation() && $user->getProfil()->getRelation()->getId() == $data['relationship']){
                if($searchService::filterAge($user, $data['minAge'], $data['maxAge'])){
                    if ($citiesRepository->find($user->getProfil()->getCity())->getCanton()->getId() == $data['canton']){

                        if (!null === $data['child'] && count($user->getProfil()->getChilds()->getValues()) === $data['child']){
                            if (count($search) < 20 ){
                                array_push($search, [$user->getId()]);
                            }
                        }
                        if (null === $data['child']){
                            if (count($search) < 20 ){
                                array_push($search, [$user->getId()]);
                            }
                        }
                    }
                    else{
                        if (count($search) < 20 ){
                            array_push($search, [$user->getId()]);
                        }
                    }
                }
            }
            else{
                if (count($search) < 20 ){
                    array_push($search, [$user->getId()]);
                }
            }
        }
        return $this->json($search);
    }

    /**
     * @param SearchService $searchService
     * @Route("/api/matching", name="api_matching", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Security("is_granted('ROLE_MEDIUM') or is_granted('ROLE_PREMIUM')")
     */
    public function autoSearch(SearchService $searchService){

        /** @var User $user */
        $user = $this->getUser();
        $result = [];
        $em = $this->getDoctrine()->getRepository(User::class);
        $usersIds = $em->findBy(['isValidated' => true, 'isConfirmed' => true, 'isDeleted' => false]);
        foreach ($usersIds as $userId){
            array_push($result, $em->find($userId));
        }
        $searches = $searchService::MatchingFilter($result, $user);
        $rows = [];
        /** @var User $search */
        foreach ($searches as $search){
            array_push($rows, $search->getId());
        }
        return $this->json($rows, 200);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/trans/matching", name="api_trans_matching", methods={"GET"})
     */
    public function autoSearchTrans(Request $request, TranslatorInterface $translator){
        return $this->json([
            'cupidon' => $translator->trans('auto.search', [], null, $request->getLocale()),
            'launch' => $translator->trans('launch', [], null, $request->getLocale())
        ], 200);
    }
}
