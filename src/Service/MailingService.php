<?php


namespace App\Service;


use App\Entity\Items;
use App\Entity\User;
use App\Mailer\Mailing;

class MailingService extends Mailing
{
    public function sendUnconnectedMail(User $user, User $target){
        if ($target->getIsNotified()){
            $this->sendMessageReceived($user, $target);
        }
    }

    public function sendRegistrationConfirmationMail(User $user, $token){
        $this->sendConfirmMessage($user, $token);
    }

    public function sendNewsletter($mail, $title, $content)
    {
        $this->sendNewsletter($mail, $title, $content);
    }

}