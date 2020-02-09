<?php


namespace App\Async;

use Symfony\Component\Process\Process;

class SixProcess
{
    public static function capture(){
        $process = new Process(['php', 'console', 'payment:capture']);
        $process->setWorkingDirectory('/var/www/html/bin/');
        $process->run();
    }
}