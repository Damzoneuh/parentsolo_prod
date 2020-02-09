<?php
namespace App\Async;

use Symfony\Component\Process\Process;

class RenewSub
{
    public static function renewSub(){
        $process = new Process(['php', 'console', 'postfinance:renew']);
        $process->setWorkingDirectory('/var/www/html/bin/');
        $process->run();
    }
}