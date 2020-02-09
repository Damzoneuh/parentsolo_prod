<?php


namespace App\Async;


use Symfony\Component\Process\Process;

class CreateItem
{
    public static function createItem($userId, $itemId){
        $process = new Process(['php', 'console', 'item:create', $itemId, $userId]);
        $process->setWorkingDirectory('/var/www/html/bin/');
        $process->run();
    }
}