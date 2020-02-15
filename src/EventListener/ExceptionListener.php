<?php


namespace App\EventListener;

use App\Mailer\Mailing;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener extends Mailing
{
    public function onKernelException(ExceptionEvent $event){
        $exception = $event->getException();
        $response = new Response();
        $this->sendException($event);
        $response->setContent($exception);
        $event->setResponse($response);
    }
}