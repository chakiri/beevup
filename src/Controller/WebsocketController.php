<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WebsocketController extends AbstractController
{
    /**
     * @Route("/chat", name="chat")
     */
    public function index()
    {
        return $this->render('websocket/home.html.twig', [
            'controller_name' => 'WebsocketController',
        ]);
    }
}
