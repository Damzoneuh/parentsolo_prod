<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param TranslatorInterface $translator
     * @param Request $request
     * @return Response
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator, Request $request): Response
    {
         if ($this->getUser() && !$this->getUser()->getIsDeleted()) {
            $this->redirectToRoute('index');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error,
            'forgot' => $translator->trans('forget.password', [], null, $request->getLocale()),
            'connection' => $translator->trans('connection.link', [], null, $request->getLocale()),
            'register' => $translator->trans('baseline', [], null, $request->getLocale()) . ' ' .  $translator->trans('baseline.red', [], null, $request->getLocale())
            ]);

    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
       return $this->redirectToRoute('index');
    }

    /**
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/user/{id}", name="api_get_user", methods={"GET"})
     */
    public function getUserRoles($id = null){
        $data = [];
        if (!$id){
            /** @var  $user User */
            $user = $this->getUser();
        }

        else{
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        }

        if ($user){
            $data['id'] = $user->getId();
            if (in_array('ROLE_ADMIN' ,$user->getRoles())){
                $data['isSub'] = true;
                $data['isPremium'] = true;
                return $this->json($data);
            }
            if (in_array('ROLE_PREMIUM' ,$user->getRoles())){
                $data['isSub'] = true;
                $data['isPremium'] = true;
                return $this->json($data);
            }
            if(in_array('ROLE_BASIC' ,$user->getRoles()) || in_array('ROLE_MEDIUM' ,$user->getRoles())){
                $data['isSub'] = true;
                $data['isPremium'] = false;
                return $this->json($data);
            }
            if(in_array('ROLE_USER' ,$user->getRoles())){
                $data['isSub'] = false;
                $data['isPremium'] = false;
                return $this->json($data);
            }
            $data['isPremium'] = false;
            $data['isSub'] = false;
            return $this->json($data);
        }
        $data['isPremium'] = false;
        $data['isSub'] = false;
        return $this->json($data);
    }
}
