<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Notification;
use App\Repository\MessageRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WebsocketController extends AbstractController
{
    /**
     * @Route("/chat/{topic}", name="chat")
     */
    public function index($topic, EntityManagerInterface $manager, MessageRepository $messageRepository, NotificationRepository $notificationRepository)
    {
        //Get all messages from topics with limit
        $messages = $messageRepository->findBy(['topic' => $topic], ['createdAt' => 'ASC']);

        //Empty notification for this topic & user
        $this->emptyNotificationTopic($topic, $manager, $notificationRepository);

        //Get all notifications for other Topics
        $notifTopics = $notificationRepository->findBy(['user' => $this->getUser()]);

        return $this->render('websocket/index.html.twig', [
            'topic' => $topic,
            'messages' => $messages,
            'notifTopics' => $notifTopics
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

        //Send data by ZMQ transporter to the Wamp server
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

    /**
     * @Route("/save_notification", name="save_notification")
     */
    public function saveNotification(EntityManagerInterface $manager, NotificationRepository $notificationRepository, UserRepository $userRepository)
    {
        $userId = $_POST['userid'];
        $topic = $_POST['topic'];

        $user = $userRepository->find($userId);

        $notification = $notificationRepository->findOneBy(['user' => $user, 'topic' => $topic]);

        if (!$notification){
            $notification = new Notification();

            $notification
                ->setUser($user)
                ->setTopic($topic)
                ->setNbMessages(1)
                ;

            $manager->persist($notification);
        }else{
            $nbMessages = $notification->getNbMessages();
            $nbMessages++;

            $notification->setNbMessages($nbMessages);

        }

        $manager->persist($notification);

        $manager->flush();

        return $this->json($notification);

    }

    protected function emptyNotificationTopic($topic, $manager, $notificationRepository)
    {
        $user = $this->getUser();

        $notification = $notificationRepository->findOneBy(['user' => $user, 'topic' => $topic]);

        if ($notification){

            $notification->setNbMessages(null);

            $manager->persist($notification);
            $manager->flush();

        }
    }
}
