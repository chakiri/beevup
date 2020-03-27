<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\Topic;
use App\Repository\MessageRepository;
use App\Repository\NotificationRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WebsocketController extends AbstractController
{
    /**
     * @Route("/chat/{name}", name="chat")
     */
    public function index(?Topic $topic, EntityManagerInterface $manager, MessageRepository $messageRepository, TopicRepository $topicRepository, NotificationRepository $notificationRepository)
    {
        //Verification passing bad topic to url
        if (!$topic){
            return $this->redirectToRoute('home');
        }
        //Get all topics
        $topics = $topicRepository->findAll();

        //Get all messages from topics with limit
        $messages = $messageRepository->findBy(['topic' => $topic], ['createdAt' => 'ASC']);

        //Empty notification for this topic & user
        $this->emptyNotificationTopic($topic, $manager, $notificationRepository);

        //Get all notifications for other Topics
        $notifTopics = $notificationRepository->findBy(['user' => $this->getUser()]);

        return $this->render('websocket/index.html.twig', [
            'topics' => $topics,
            'currentTopic' => $topic,
            'messages' => $messages,
            'notifTopics' => $notifTopics
        ]);
    }

    /**
     * @Route("/sender", name="sender")
     */
    public function sender(EntityManagerInterface $manager, TopicRepository $topicRepository)
    {
        $topicName = $_POST['topic'];
        $content = $_POST['message'];

        //Get current user
        $user = $this->getUser();

        $entryData = [
            'user' => $user->getProfile()->getFirstname(),
            'topic' => $topicName,
            'message' => $content,
        ];

        //Send data by ZMQ transporter to the Wamp server
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
        $socket->connect("tcp://127.0.0.1:5555");

        $socket->send(json_encode($entryData));

        //Get topic object
        $topic = $topicRepository->findOneBy(['name' => $topicName]);

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
    public function saveNotification(EntityManagerInterface $manager, NotificationRepository $notificationRepository, UserRepository $userRepository, TopicRepository $topicRepository)
    {
        $userId = $_POST['userid'];
        $topicName = $_POST['topic'];

        //Find objects
        $user = $userRepository->find($userId);
        $topic = $topicRepository->findOneBy(['name' => $topicName]);

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

    protected function emptyNotificationTopic(Topic $topic, $manager, $notificationRepository)
    {
        $user = $this->getUser();

        $notification = $notificationRepository->findOneBy(['user' => $user, 'topic' => $topic]);

        if ($notification){
            $notification->setNbMessages(null);

            $manager->persist($notification);
            $manager->flush();
        }

        return $this->json($notification);
    }
}
