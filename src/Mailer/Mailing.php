<?php


namespace App\Mailer;

use App\Entity\Items;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class Mailing
{
    private $_mailer;
    private $_message;
    private $_twig;
    private $_translator;

    public function __construct(EntityManagerInterface $entityManager ,\Swift_Mailer $mailer, \Twig_Environment $container, TranslatorInterface $translator)
    {
        $this->_mailer = $mailer;
        $this->_message = new \Swift_Message();
        $this->_twig = $container;
        $this->_translator = $translator;
    }

    public function sendConfirmMessage(User $user, $token) : void {
        $this->_message->setSubject($this->_translator->trans('register.title', [], null, strtolower($user->getLangForModeration())));
        $this->_message->setTo($user->getEmail());
        $this->_message->setFrom('message@parentsolo.ch');
        $this->_message->setBody($this->_twig->render('email/register.html.twig', [
            'user' => $user,
            'validate' => $this->_translator->trans('validate', [], null, strtolower($user->getLangForModeration())),
            'content' => $this->_translator->trans('register.text', [], null, strtolower($user->getLangForModeration())),
            'token' => $token,
            'title' => $this->_translator->trans('register.title', [], null, strtolower($user->getLangForModeration())),
            'links' => [
                'testimony' => $this->_translator->trans('testimony.link', [], null, strtolower($user->getLangForModeration())),
                'diary' => $this->_translator->trans('diary', [], null, strtolower($user->getLangForModeration())),
                'first' => $this->_translator->trans('text.email.first', [], null, strtolower($user->getLangForModeration())),
                'second' => $this->_translator->trans('text.email.second', [], null, strtolower($user->getLangForModeration()))
            ]
        ]), 'text/html');
        $this->_mailer->send($this->_message);
    }

    public function sendResetMessage(User $user, $token){
        $this->_message->setSubject($this->_translator->trans('register.title', [], null, strtolower($user->getLangForModeration())));
        $this->_message->setTo($user->getEmail());
        $this->_message->setFrom('message@parentsolo.ch');
        $this->_message->setBody($this->_twig->render('email/reset.html.twig', [
            'user' => $user,
            'validate' => $this->_translator->trans('validate', [], null, strtolower($user->getLangForModeration())),
            'content' => $this->_translator->trans('reset.text', [], null, strtolower($user->getLangForModeration())),
            'token' => $token,
            'title' => $this->_translator->trans('reset.title', [], null, strtolower($user->getLangForModeration())),
            'links' => [
                'testimony' => $this->_translator->trans('testimony.link', [], null, strtolower($user->getLangForModeration())),
                'diary' => $this->_translator->trans('diary', [], null, strtolower($user->getLangForModeration())),
                'first' => $this->_translator->trans('text.email.first', [], null, strtolower($user->getLangForModeration())),
                'second' => $this->_translator->trans('text.email.second', [], null, strtolower($user->getLangForModeration()))
            ]
        ]), 'text/html');
        $this->_mailer->send($this->_message);
    }

    public function sendException(ExceptionEvent $event){
        $e = $event->getException();
        $message = new \Swift_Message();
        $message->setSubject('Exception');
        $message->setTo('damien@backndev.fr');
        $message->setFrom('exception@parentsolo.ch');
        $message->setBody('
            <h1>Exception Caught</h1>
            <p>message : '. $e->getMessage() . '</p>
            <p>Line : ' .$e->getLine() .'</p>
            <p>error code : ' . $e->getCode()  . '</p>
            <p>stack trace : ' . $e->getTraceAsString() . '</p>
           
        ', 'text/html');
        $this->_mailer->send($message);
    }

    public function sendMessageReceived(User $user, User $target){
        $message = $this->_message;
        $message->setTo($target->getEmail());
        $message->setFrom('message@parentsolo.ch');
        $message->setSubject($this->_translator->trans('email.message.receive.title', [], null, strtolower($target->getLangForModeration())));
        $message->setBody($this->_twig->render('email/message-received.html.twig', [
            'user' => $user,
            'content' => $this->_translator->trans('email.message.receive.text', [], null, strtolower($target->getLangForModeration())),
            'title' => $this->_translator->trans('new.notification', [], null, strtolower($target->getLangForModeration())),
            'button' => $this->_translator->trans('discover', [], null, strtolower($target->getLangForModeration())),
            'links' => [
                'testimony' => $this->_translator->trans('testimony.link', [], null, strtolower($target->getLangForModeration())),
                'diary' => $this->_translator->trans('diary', [], null, strtolower($target->getLangForModeration())),
                'first' => $this->_translator->trans('text.email.first', [], null, strtolower($target->getLangForModeration())),
                'second' => $this->_translator->trans('text.email.second', [], null, strtolower($target->getLangForModeration()))
            ]
        ]), 'text/html');
        $this->_mailer->send($message);
    }

    public function sendNotification(User $target, User $user, $action, $content, $type){
        $message = $this->_message;
        $message->setTo($target->getEmail());
        $message->setFrom('message@parentsolo.ch');
        $message->setSubject($action);
        $message->setBody($this->_twig->render($type == 'flower' ? 'email/flower-received.html.twig' : 'email/visit-received.html.twig', [
            'user' => $user,
            'content' => $content,
            'title' => $action,
            'button' => $this->_translator->trans('discover', [], null, strtolower($target->getLangForModeration())),
            'links' => [
                'testimony' => $this->_translator->trans('testimony.link', [], null, strtolower($target->getLangForModeration())),
                'diary' => $this->_translator->trans('diary', [], null, strtolower($target->getLangForModeration())),
                'first' => $this->_translator->trans('text.email.first', [], null, strtolower($target->getLangForModeration())),
                'second' => $this->_translator->trans('text.email.second', [], null, strtolower($target->getLangForModeration()))
            ]
        ]), 'text/html');
        $this->_mailer->send($message);
    }

    public function sendAdminMail(User $user, $content){
        $message = $this->_message;
        $message->setSubject($this->_translator->trans('new.notification', [], null, strtolower($user->getLangForModeration())));
        $message->setTo($user->getEmail());
        $message->setFrom('message@parentsolo.ch');
        $message->setBody($this->_twig->render('email/newsletter.html.twig', [
            'user' => $user,
            'content' => $content,
            'title' => $this->_translator->trans('new.notification', [], null, strtolower($user->getLangForModeration())),
            'links' => [
                'testimony' => $this->_translator->trans('testimony.link', [], null, strtolower($user->getLangForModeration())),
                'diary' => $this->_translator->trans('diary', [], null, strtolower($user->getLangForModeration())),
                'first' => $this->_translator->trans('text.email.first', [], null, strtolower($user->getLangForModeration())),
                'second' => $this->_translator->trans('text.email.second', [], null, strtolower($user->getLangForModeration()))
            ]
        ]), 'text/html');
        $this->_mailer->send($message);
    }

    public function sendNewsletter($mail, $title, $content){
        $message = $this->_message;
        $message->setSubject($title);
        $message->setFrom('message@parentsolo.ch');
        $message->setTo($mail);
        $message->setBody('<p>' . $content . '</p>', 'text/html');
        $this->_mailer->send($message);
    }

}