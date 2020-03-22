<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WebsocketController extends AbstractController
{
    /**
     * @Route("/chat/{topic}", name="chat")
     */
    public function index($topic, MessageRepository $repository)
    {
        //Get all messages from topics with limit
        $messages = $repository->findBy(['topic' => $topic], ['createdAt' => 'ASC'], 20);

        return $this->render('websocket/index.html.twig', [
            'topic' => $topic,
            'messages' => $messages
        ]);
    }

    /**
     * @Route("/sender", name="sender")
     */
    public function sender(EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        $topic = $_POST['topic'];
        $content = $_POST['message'];

        $entryData = [
            'user' => $user->getProfile()->getFirstname(),
            'topic' => $topic,
            'message' => $content,
        ];

        //Send data by ZMQ transporteur to the Wamp server
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
        $socket->connect("tcp://127.0.0.1:5555");

        $socket->send(json_encode($entryData));

        //Stock in database
        $message = new Message();

        $message
            ->setUser($user)
            ->setContent($content)
            ->setTopic($topic)
            ->setCreatedAt(new \DateTime())
        ;

        $manager->persist($message);
        $manager->flush();

        return $this->json($entryData);
    }
}
