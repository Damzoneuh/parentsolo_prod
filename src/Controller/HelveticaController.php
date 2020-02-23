<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HelveticaController extends AbstractController
{
    /**
     * @Route("/admin/helvetica", name="admin_helvetica")
     */
    public function index()
    {
        $helveticas = $this->getDoctrine()->getRepository(User::class)->getHelvetica();
        $data = [];
        /** @var User $helvetica */
        foreach ($helveticas as $helvetica){
            $message = $this->getDoctrine()->getRepository(Messages::class)->findOneBy(['messageTo' => $helvetica->getId(), 'isRead' => false, 'isClose' => null]);
            $data[$helvetica->getId()] = $message;
        }
        return $this->render('helvetica/index.html.twig', [
            'helveticas' => $helveticas,
            'messages' => $data
        ]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/show/helvetica/{id}", name="admin_conversation_show")
     */
    public function getAllMessages($id){
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $messages = $this->getDoctrine()->getRepository(Messages::class)->findInnerMessages($id);
        $data = [];
        /** @var Messages $message */
        foreach ($messages as $message){
            $data[$message->getMessageFrom()] = [
                'message' => $message,
                'user' => $this->getDoctrine()->getRepository(User::class)->find($message->getMessageFrom()),
                'helvetica' => $user
            ];
        }
        return $this->render('helvetica/get-all-messages.html.twig', ['messages' => $data]);
    }

    /**
     * @param Request $request
     * @param $target
     * @param $helvetica
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/admin/reply/{target}/{helvetica}", name="admin_reply")
     */
    public function reply(Request $request, $target, $helvetica){
        $messages = $this->getDoctrine()->getRepository(Messages::class)->getConversation($helvetica, $target);

        $form = $this->createFormBuilder()
            ->add('content', TextareaType::class, [
                'label' => 'Réponse'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer'
            ])
            ->getForm();
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $data = [];

        if (count($messages) > 0){
            /** @var Messages $message */
            foreach ($messages as $message){
                $message->setIsRead(true);
                $em->flush();
                $data[$message->getId()] = [
                    'content' => urldecode($message->getContent()),
                    'from' => $message->getMessageFrom(),
                    'to' => $message->getMessageTo()
                ];
            }
        }

        if ($form->isSubmitted() && $form->isValid()){

            $formData = $form->getData();
            $newMessage = new Messages();
            $newMessage->setMessageTo($target);
            $newMessage->setMessageFrom($helvetica);
            $newMessage->setContent(urldecode($formData['content']));
            $newMessage->setIsRead(false);
            $em->persist($newMessage);
            $em->flush();

            return $this->redirectToRoute('admin_conversation_show', ['id' => $helvetica]);
        }

        return $this->render('helvetica/reply.html.twig', [
            'form' => $form->createView(),
            'messages' => $data,
            'helvetica' => $helvetica
        ]);
    }
}
