<?php

namespace App\Controller;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ParametersController extends AbstractController
{
    /**
     * @Route("/parameters", name="parameters")
     * @param TranslatorInterface $translator
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(TranslatorInterface $translator, Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $accountSettingsForm = $this->createFormBuilder()
//            ->add('gender',ChoiceType::class, [
//                'choices' => [
//                    $translator->trans('man', [], null, $request->getLocale()) => true,
//                    $translator->trans('woman', [], null, $request->getLocale()) => false
//                ],
//                'label' => $translator->trans('gender', [], null, $request->getLocale()),
//                'data' => $user->getProfil()->getIsMan() ? $translator->trans('man', [], null, $request->getLocale()) :
//                    $translator->trans('woman', [], null, $request->getLocale())
//            ])
            ->add('name', TextType::class, [
                'label' => $translator->trans('name', [], null, $request->getLocale()),
                'data' => $user->getName()
            ])
            ->add('firstname', TextType::class, [
                'label' => $translator->trans('firstname', [], null, $request->getLocale()),
                'data' => $user->getFirstName()
            ])
            ->add('address', TextType::class, [
                'label' => $translator->trans('address', [], null, $request->getLocale()),
                'data' => $user->getAddress()
            ])
            ->add('npa', NumberType::class, [
                'attr' => [
                    'pattern' => '[0-9]{4}',
                    'class' => 'marg-left-10'
                ],
                'label' => $translator->trans('npa', [], null, $request->getLocale()),
                'data' => $user->getNpa()
            ])
            ->add('communication_lang', ChoiceType::class,
                [
                    'choices' => [
                        'FR' => 'FR',
                        'DE' => 'DE',
                        'EN' => 'EN',
                    ],
                    'label' => $translator->trans('communication.lang', [], null, $request->getLocale()),
                    'data' => $user->getLangForModeration()
                ])
            ->add('is_looking_for', ChoiceType::class, [
                'choices' => [
                    $translator->trans('man', [], null, $request->getLocale()) => true,
                    $translator->trans('woman', [], null, $request->getLocale()) => false,
                    $translator->trans('both', [], null, $request->getLocale()) => null
                ],
                'label' => $translator->trans('is.looking.for', [], null, $request->getLocale()),
                'data' => $user->getIsLookingSex() ? $translator->trans('man', [], null, $request->getLocale()) : $user->getIsLookingSex() === null ? $translator->trans('both', [], null, $request->getLocale()) : $translator->trans('woman', [], null, $request->getLocale())
            ])
            ->add('submit', SubmitType::class, [
                'label' => $translator->trans('validate', [], null, $request->getLocale()),
                'attr' => [
                    'class' => 'btn btn-group btn-success'
                ]
            ])
            ->getForm();
        $accountSettingsForm->handleRequest($request);


        $notificationForm = $this->createFormBuilder()
            ->add('notification', CheckboxType::class, [
                'label' => $translator->trans('notification.checkbox', [], null, $request->getLocale()),
                'required' => false,
                'data' => $user->getIsNotified() ? true : false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => $translator->trans('validate', [], null, $request->getLocale()),
                'attr' => [
                    'class' => 'btn btn-group btn-outline-danger'
                ]
            ])
            ->getForm();

        $notificationForm->handleRequest($request);

        if ($notificationForm->isSubmitted() && $notificationForm->isValid()){
            $em = $this->getDoctrine()->getManager();
            $data = $notificationForm->getData();
            $user->setIsNotified($data['notification']);
            $em->flush();

            return $this->redirectToRoute('parameters');
        }

        if ($accountSettingsForm->isSubmitted() && $accountSettingsForm->isValid()){
            $em = $this->getDoctrine()->getManager();
            $data = $accountSettingsForm->getData();
            $user->getProfil()->setIsMan($data['gender']);
            $em->persist($user);
            $user->setName($data['name']);
            $user->setFirstName($data['firstname']);
            $user->setAddress($data['address']);
            $user->setNpa($data['npa']);
            $user->setLangForModeration($data['communication_lang']);
            $user->setIsLookingSex($data['is_looking_for']);
           // $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('parameters');
        }

        $trans = [
            'settings' => $translator->trans('account settings', [], null, $request->getLocale()),
            'p1' => $translator->trans('parameter.paragraph1', [], null, $request->getLocale()),
            'p2' => $translator->trans('parameter.paragraph2', [], null, $request->getLocale()),
            'pred' => $translator->trans('parameter.paragraph.red', [], null, $request->getLocale()),
            'notification' => $translator->trans('account notification', [], null, $request->getLocale()),
            'textnotif' => $translator->trans('text.email.notif', [], null, $request->getLocale()),
            'alias' => $translator->trans('alias', [], null, $request->getLocale()),
            'connection' => $translator->trans('connection settings', [], null, $request->getLocale()),
            'change_password' => $translator->trans('change password', [], null, $request->getLocale()),
            'let_testy' => $translator->trans('let.testimony', [], null, $request->getLocale()),
            'faq' => $translator->trans('faq', [], null, $request->getLocale()),
            'contact' => $translator->trans('contact', [],null, $request->getLocale()),
            'stopsub' => $translator->trans('stop subscribe', [], null, $request->getLocale()),
            'deadline' => $translator->trans('deadline', [], null, $request->getLocale()),
            'man' => $translator->trans('man', [], null, $request->getLocale()),
            'woman' => $translator->trans('woman', [], null, $request->getLocale()),
            'gender' => $translator->trans('gender', [], null, $request->getLocale())
        ];

        return $this->render('parameters/index.html.twig', [
            'settingsForm' => $accountSettingsForm->createView(),
            'notificationForm' => $notificationForm->createView(),
            'trans' => $trans,
            'isMan' => $user->getProfil()->getIsMan()
        ]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/unsbsribe", name="unsubscribe")
     * @throws \Exception
     */
    public function deleteSubscribe(Request $request, TranslatorInterface $translator, \Swift_Mailer $mailer){
        $form = $this->createFormBuilder()
            ->add('account', CheckboxType::class, [
                'label' => $translator->trans('delete.account', [], null, $request->getLocale()),
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => $translator->trans('delete.sub.button', [], null, $request->getLocale())
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            /** @var User $user */
            $user = $this->getUser();
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $token = self::genToken();
            $user->setResetToken($token);
            $em->flush();
            $message = new \Swift_Message();
            $message->setFrom('noreply@parentsolo.ch');
            $message->setTo($user->getEmail());
            $message->setSubject($translator->trans('unsubscribe', [], null, $request->getLocale()));
            $message->setBody('<div>' . $translator->trans('unsubribe.text', [], null, $request->getLocale()) .
                '</div><a href="'. $this->generateUrl('unsubscribe_mail', ['token' => $token, 'account' => $data['account']], UrlGeneratorInterface::ABSOLUTE_URL) . '" class="btn btn-group btn-primary">' . $translator->trans('unsubscribe', [], null, $request->getLocale()) . '</a>'
                , 'text/html');

            $mailer->send($message);

            return $this->redirectToRoute('index');
        }
        return $this->render('parameters/unsubscribe.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param $token
     * @param null $account
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/unsbscribe/mail/{token}/{account}", name="unsubscribe_mail")
     */
    public function removeSubMail($token, $account = null){
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if ($token == $user->getResetToken()){
            if ($account){
                $em->remove($user);
                $em->flush();
                return $this->redirectToRoute('app_logout');
            }
            if ($user->getSubscribe()){
                $em->remove($user->getSubscribe());
                $user->setRoles(["ROLE_USER"]);
                $user->setResetToken(null);
                $em->flush();
                return $this->redirectToRoute('app_logout');
            }
            $user->setRoles(["ROLE_USER"]);
            $user->setResetToken(null);
            $em->flush();
            return $this->redirectToRoute('app_logout');
        }
        return $this->redirectToRoute('parameters');
    }

    /**
     * @return string
     * @throws \Exception
     */
    private static function genToken() : string {
        return $token = bin2hex(random_bytes(36));
    }
}
