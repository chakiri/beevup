<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WebsocketController extends AbstractController
{
    /**
     * @Route("/chat/{topic}", name="chat")
     */
    public function index($topic)
    {
        return $this->render('websocket/index.html.twig', [
            'topic' => $topic,
        ]);
    }

    /**
     * @Route("/sender", name="sender")
     */
    public function sender()
    {
        $username = $this->getUser()->getProfile()->getFirstname();

        $entryData = [
            'user' => $username,
            'topic' => $_POST['topic'],
            'message'    =>  $_POST['message'],
        ];

        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
        $socket->connect("tcp://127.0.0.1:5555");

        $socket->send(json_encode($entryData));

        return $this->json($entryData);
    }
}
