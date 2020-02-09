<?php


namespace App\EventListener;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    private $defaultLocale;
    private $_em;

    public function __construct(EntityManagerInterface $em, $defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
        $this->_em = $em;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
       // dump($request->getHttpHost()); die();
        /** @var User $user */
        $user = $this->_em->getRepository(User::class)->findOneBy(['email' => $request->getSession()->get('_security.last_username')]);
        if ($request->getHttpHost() === "singleltern.ch" || $request->getHttpHost() === 'www.singleltern.ch'){
            $this->defaultLocale = 'de';
        }
//        else{
//            $this->defaultLocale = 'fr';
//        }
        if (!$request->hasPreviousSession()) {
            return;
        }

        // try to see if the locale has been set as a _locale routing parameter
        if ($locale = $request->cookies->get('_locale')) {
            $request->setLocale($locale);
            if ($user) {
                $user->setLangForModeration(strtoupper($locale));
                $this->_em->flush();
            }
        } else {
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
            if ($user){
                $user->setLangForModeration(strtoupper($request->getSession()->get('_locale')));
                $this->_em->flush();
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 0]],
        ];
    }
}