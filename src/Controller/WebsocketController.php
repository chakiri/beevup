<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\Topic;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\NotificationRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WebsocketController extends AbstractController
{
    /**
     * @Route("/chat/private/{id}", name="chat_private")
     * @Route("/chat/{name}", name="chat_topic")
     */
    public function index(?Topic $topic, ?User $user, Request $request , MessageRepository $messageRepository, NotificationRepository $notificationRepository, UserRepository $userRepository)
    {
        //Verification passing bad subject to url
        if (!$topic && !$user){
            return $this->redirectToRoute('home');
        }

        //Get all topics of user
        $topics = $this->getUser()->getTopics();

        //Get all users
        $users = $userRepository->findAll();

        if ($request->get('_route') == 'chat_private'){
            //Assign user to the subject
            $subject = $user->getId();
            //Get all messages from receiver with limit
            $messages = $messageRepository->findMessagesBetweenUserAndReceiver($this->getUser(), $user);
        }elseif($request->get('_route') == 'chat_topic'){
            //Assign topic to the subject
            $subject = $topic->getName();
            //Get all messages from topics with limit
            $messages = $messageRepository->findBy(['topic' => $topic], ['createdAt' => 'ASC']);
        }

        //Empty notification for this topic & user
        //$this->emptyNotificationTopic($topic, $manager, $notificationRepository);

        //Get all notifications for other Topics
        //$notifTopics = $notificationRepository->findBy(['user' => $this->getUser()]);

        return $this->render('websocket/index.html.twig', [
            'topics' => $topics,
            'users' => $users,
            'subject' => $subject,
            'messages' => $messages,
            //'notifTopics' => $notifTopics,
            'isPrivate' => $request->get('_route') == 'chat_private'
        ]);
    }

    /**
     * Send data to WAMP Server with ZMQ
     *
     * @Route("/sender", name="sender")
     */
    public function sender(EntityManagerInterface $manager, TopicRepository $topicRepository, UserRepository $userRepository)
    {
        $subject = $_POST['subject'];
        $from = $_POST['from'];
        $content = $_POST['message'];
        $isPrivate = $_POST['isprivate'];

        //Get current user
        $user = $this->getUser();

        $entryData = [
            'user' => $user->getProfile()->getFirstname(),
            'from' => $from,
            'subject' => $subject,
            'message' => $content,
            'isprivate' => $isPrivate,
        ];

        //Send data by ZMQ transporter to the Wamp server
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
        $socket->connect("tcp://127.0.0.1:5555");

        $socket->send(json_encode($entryData));

        //Save data in DB
        $message = new Message();

        $message
            ->setUser($user)
            ->setContent($content)
            ->setCreatedAt(new \DateTime())
        ;

        if ($isPrivate == false){
            //Get topic object
            $topic = $topicRepository->findOneBy(['name' => $subject]);

            $message
                ->setTopic($topic)
                ->setIsPrivate($isPrivate)
            ;

        }else {
            //Get topic object
            $receiver = $userRepository->findOneBy(['id' => $subject]);

            $message
                ->setReceiver($receiver)
                ->setIsPrivate($isPrivate)
            ;
        }

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
