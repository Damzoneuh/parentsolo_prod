<?php


namespace App\Async;
use Symfony\Component\Process\Process;

class NewsLetter
{
    public static function sendNewsLetter($id){
        $process = new Process(['php', 'console', 'newsletter:send', $id]);
        $process->setWorkingDirectory('/var/www/html/bin/');
        $process->run();
    }
}