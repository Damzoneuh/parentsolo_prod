<?php


namespace App\Command;


use App\Service\ItemsService;
use App\Service\PostFinanceService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RenewCommand extends Command
{
    protected static $defaultName = 'postfinance:renew';
    private $_postFinanceService;

    public function __construct(PostFinanceService $postFinanceService)
    {
        $this->_postFinanceService = $postFinanceService;
        parent::__construct();
    }

    protected function configure()
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $this->_postFinanceService->renew();
    }
}