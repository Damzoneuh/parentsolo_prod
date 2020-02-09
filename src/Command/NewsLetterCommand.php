<?php


namespace App\Command;


use App\Entity\NewsLetter;
use App\Entity\User;
use App\Service\MailingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


class NewsLetterCommand extends Command
{
    protected static $defaultName = 'newsletter:send';
    private $_em;
    private $_mailer;
    private $_twig;
    private $_translator;

    public function __construct(\Swift_Mailer $mailer, EntityManagerInterface $em, \Twig_Environment $container, TranslatorInterface $translator)
    {
        parent::__construct();
        $this->_em = $em;
        $this->_mailer = $mailer;
        $this->_twig = $container;
        $this->_translator = $translator;

    }

    protected function configure()
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'id of newsletter');
        //$this->addArgument('userId', InputArgument::REQUIRED, 'id of the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       $users = $this->_em->getRepository(User::class)->getNotifiedUsers();
       $newsLetter = $this->_em->getRepository(NewsLetter::class)->find($input->getArgument('id'));
       if ($users) {
           foreach ($users as $user){
               /** @var User $user */
               $lang = ucfirst(strtolower($user->getLangForModeration() ? $user->getLangForModeration() : 'FR'));
               $title = 'get'.$lang.'Title';
               $text = 'get'.$lang.'Text';
               $message = new \Swift_Message();
               $message->setSubject($newsLetter->$title());
               $message->setFrom('message@parentsolo.ch');
               $message->setTo($user->getEmail());
               $message->setBody($this->_twig->render('email/newsletter.html.twig',
                   [
                       'content' => $newsLetter->$text(),
                       'title' => $newsLetter->$title(),
                       'links' => [
                           'testimony' => $this->_translator->trans('testimony.link', [], null, strtolower($lang)),
                           'diary' => $this->_translator->trans('diary', [], null, strtolower($lang)),
                           'first' => $this->_translator->trans('text.email.first', [], null, strtolower($lang)),
                           'second' => $this->_translator->trans('text.email.second', [], null, strtolower($lang))
                       ]]), 'text/html');
               $this->_mailer->send($message);
               $output->writeln($newsLetter->$text());
           }
       }
    }
}