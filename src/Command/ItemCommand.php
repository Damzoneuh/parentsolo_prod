<?php


namespace App\Command;


use App\Service\ItemsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ItemCommand extends Command
{
    protected static $defaultName = 'item:create';
    private $_itemService;

    public function __construct(ItemsService $itemsService)
    {
        $this->_itemService = $itemsService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'The items\'s Id');
        $this->addArgument('userId', InputArgument::REQUIRED, 'The user\'s Id');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if($this->_itemService->createItem($input->getArgument('id'), $input->getArgument('userId'))){
            $output->writeln('ok');
        }
        else{
            $output->writeln('no');
        }
    }
}