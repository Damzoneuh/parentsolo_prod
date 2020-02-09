<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class PublishController extends AbstractController
{
    //TODO install composer require api
    
    public function __invoke(Publisher $publisher): Response
    {
        $update = new Update(
            'https://parentsolo.backndev.fr/books/1',
            json_encode(['status' => 'OutOfStock'])
        );

        // The Publisher service is an invokable object
        $publisher($update);

        return new Response('published!');
    }

    /**
     * @Route("/books/1")
     */
    public function test(){
        return $this->json('ok ok');
    }
}