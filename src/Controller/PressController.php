<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PressController extends AbstractController
{
    /**
     * @Route("/press", name="press")
     */
    public function index()
    {
        return $this->render('press/index.html.twig', [
            'controller_name' => 'PressController',
        ]);
    }
}
