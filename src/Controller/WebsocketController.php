<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Topic;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\NotificationRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use App\Service\EmptyNotification;
use App\Service\SaveNotification;
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
    public function index(?Topic $topic, ?User $user, Request $request, MessageRepository $messageRepository, NotificationRepository $notificationRepository, UserRepository $userRepository, EmptyNotification $emptyNotification)
    {
        //Verification passing bad subject to url
        if (!$topic && !$user){
            return $this->redirectToRoute('home');
        }

        if ($request->get('_route') == 'chat_private'){
            //Empty notification for user
            $emptyNotification->empty($this->getUser(), $user);
            //Assign user to the subject
            $subject = $user->getId();
            //Get all messages from receiver with limit
            $messages = $messageRepository->findMessagesBetweenUserAndReceiver($this->getUser(), $user);
        }elseif($request->get('_route') == 'chat_topic'){
            //Empty notification for topic
            $emptyNotification->empty($this->getUser(), $topic);
            //Assign topic to the subject
            $subject = $topic->getName();
            //Get all messages from topics with limit
            $messages = $messageRepository->findBy(['topic' => $topic], ['createdAt' => 'ASC']);
        }

        return $this->render('websocket/index.html.twig', [
            'topics' => $this->getUser()->getTopics(),
            'users' => $userRepository->findAll(),
            'notifications' => $notificationRepository->findBy(['user' => $this->getUser()]),
            'subject' => $subject,
            'messages' => $messages,
            'isPrivate' => $request->get('_route') == 'chat_private',
            'user' => $user ?: null,
            'topic' => $topic ?: null
        ]);
    }

    /**
     * Send data to WAMP Server with ZMQ
     *
     * @Route("/sender", name="sender")
     */
    public function sender(EntityManagerInterface $manager, TopicRepository $topicRepository, UserRepository $userRepository, NotificationRepository $notificationRepository)
    {

        $subject = $_POST['subject'];
        $from = $_POST['from'];
        $content = $_POST['message'];
        $isPrivate = $_POST['isprivate'];

        //Get current user
        $user = $this->getUser();

        //Get notifs receiver
        $notifications = $notificationRepository->findOneBy(['user' => $subject, 'receiver' => $from]);
        if ($notifications) $nbNotifications = $notifications->getNbMessages();

        $entryData = [
            'user' => $user->getProfile()->getFirstname(),
            'from' => $from,
            'subject' => $subject,
            'message' => $content,
            'isprivate' => $isPrivate,
            'nbNotifications' => $nbNotifications ?? null
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
    public function saveNotification(SaveNotification $saveNotification)
    {
        $user = $_POST['user'];
        $subject = $_POST['subject'];

        //Add notification to user
        $saveNotification->save($user, $subject);

        return $this->json('save notification');
    }
}
