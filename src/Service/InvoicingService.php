<?php


namespace App\Service;


use App\Entity\Items;

class InvoicingService
{
    private $_mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->_mailer = $mailer;
    }

    public function sendInvoicingMail(Items $items){
        //TODO send aa mail where the invoice is complete can ba a process
    }
}