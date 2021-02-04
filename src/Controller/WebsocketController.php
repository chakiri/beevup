<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\MessageNotificationRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use App\Service\Mailer;
use App\Service\EmptyMessageNotification;
use App\Service\SaveNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WebsocketController extends AbstractController
{
    private $userTypeRepo;
    private $userRepo;
    private $mailer;

    public function __construct(UserTypeRepository $userTypeRepo, UserRepository $userRepo, Mailer $mailer)
    {
        $this->userTypeRepo = $userTypeRepo;
        $this->userRepo = $userRepo;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/chat/private/{id}", name="chat_private")
     * @Route("/chat/{name}", name="chat_topic")
     */
    public function index(?Topic $topic, ?User $user, Request $request, MessageRepository $messageRepository, MessageNotificationRepository $notificationRepository, EmptyMessageNotification $emptyMessageNotification)
    {
        //Verification passing bad subject to url
        if (!$topic && !$user) return $this->render('bundles/TwigBundle/Exception/error403.html.twig');

        //If profile is incomplete
        if ($this->getUser()->getProfile()->getIsCompleted() == false){
            $this->addFlash('warning', 'Veuillez completer votre profile pour accéder au chat !');

            return $this->redirectToRoute('profile_edit', [
                'id' => $this->getUser()->getProfile()->getId()
            ]);
        }

        if ($request->get('_route') == 'chat_private'){
            //Assign user to the subject
            $subject = $user->getId();
            //Get all messages from receiver with limit
            $messages = $messageRepository->findMessagesBetweenUserAndReceiver($this->getUser(), $user);
            //Empty notification for user
            $emptyMessageNotification->empty($this->getUser(), $user);

        }elseif($request->get('_route') == 'chat_topic'){
            //if user not having this topic
            if (!in_array($topic, $this->getUser()->getTopics()->toArray())) return $this->render('bundles/TwigBundle/Exception/error403.html.twig');

            //Assign topic to the subject
            $subject = $topic->getName();
            //Get all messages from topics with limit
            $messages = $messageRepository->findBy(['topic' => $topic], ['createdAt' => 'ASC']);
            //Empty notification for topic
            $emptyMessageNotification->empty($this->getUser(), $topic);
        }

        //Get recent users in conversation
        $users = $this->getRecentUsersChat($messageRepository);

        //Get general topic in the first
        $topicsList = $this->getUser()->getTopics();
        $topics = $this->handleTopicsList($topicsList);

        return $this->render('websocket/index3.html.twig', [
            'topics' => $topics,
            'users' => $users,
            'notifications' => $notificationRepository->findBy(['user' => $this->getUser()]),
            'subject' => $subject,
            'messages' => $messages,
            'isPrivate' => $request->get('_route') == 'chat_private',
            'user' => $user ?: null,
            'topic' => $topic ?: null
        ]);
    }

    public function handleTopicsList($topics)
    {
        $newTopics = [];
        foreach ($topics as $topic){
            if(substr($topic->getName(), 0, 8) === "general-"){
                $generalTopic = $topic;
            }else{
                array_push($newTopics, $topic);
            }
        }
        if (isset($generalTopic)) array_unshift($newTopics, $generalTopic);

        return $newTopics;
    }

    /**
     * Send data to WAMP Server with ZMQ
     *
     * @Route("/sender", name="sender")
     */
    public function sender()
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
            'message' => nl2br($content),
            'isprivate' => $isPrivate,
        ];

        //Send data by ZMQ transporter to the Wamp server
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
        $socket->connect("tcp://127.0.0.1:5555");

        $socket->send(json_encode($entryData));

        return $this->json($entryData);
    }

    /**
     * @Route("/save_notification", name="save_notification")
     */
    public function saveNotification(SaveNotification $saveNotification, UserRepository $userRepository)
    {
        $userid = $_POST['user'];
        $subject = $_POST['subject'];

        $user = $userRepository->find($userid);

        //Add notification to user
        $saveNotification->save($user, $subject);

        return $this->json($userid);
    }

    /**
     * @Route("/check_first_message", name="check_first_message")
     */
    public function checkFirstMessage(MessageRepository $messageRepository, UserRepository $userRepository, Mailer $mailer)
    {
        $userId = $_POST['userid'];
        $receiverid = $_POST['receiverid'];

        $user = $userRepository->findOneBy(['id' => $userId]);
        $receiver = $userRepository->findOneBy(['id' => $receiverid]);

        //Get all messages between user and subject
        $messages = $messageRepository->findMessagesBetweenUserAndReceiver($user, $receiver);

        if ($user && count($messages) === 1){
            $params = ['sender' => $user->getProfile()->getLastname() . ' ' . $user->getProfile()->getFirstname(), 'senderCompany' => $user->getCompany()->getName(), 'url' => $this->generateUrl('chat_private', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL)];
            $mailer->sendEmailWithTemplate($receiver->getEmail(), $params, 7);
        }

        return $this->json($messages);
    }

    /**
     * Get all recent users in chating
     * @param MessageRepository $messageRepository
     * @return array
     */
    private function getRecentUsersChat(MessageRepository $messageRepository): array
    {
        $messages = $messageRepository->findMessagesBetweenUserAndOthers($this->getUser());
        $users = [];
        foreach($messages as $message){
            if ($message->getIsPrivate() == true) {
                if ($message->getUser() == $this->getUser()){
                    if (!in_array($message->getReceiver(), $users)){
                        array_push($users, $message->getReceiver());
                    }
                }elseif ($message->getReceiver() == $this->getUser()){
                    if (!in_array($message->getUser(), $users)){
                        array_push($users, $message->getUser());
                    }
                }
            }
        }

        return $users;
    }

    /**
     * Send daily email
     * @param User $user
     * @param int $notificationNumber
     */
    public function sendDailyEmail(User $user, $notificationNumber): void
    {
        $userTypePatron = $this->userTypeRepo->findOneBy(['id'=> 4]);
        $url = $this->generateUrl('chat_topic', ['name' => 'general-' . $user->getStore()->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);
        $subject = ($notificationNumber != 1) ? 'nouveaux messages vous attendent sur Beev\'Up' : 'nouveau message vous attend sur Beev\'Up';

        if ($notificationNumber == 1) $message = "Vous avez 1 nouveau message non lu.";
        else $message = "Vous avez " . $notificationNumber . " nouveaux messages non lus.";

        $params = ['message' => $message, 'url' => $url];
        $this->mailer->sendEmailWithTemplate($user->getEmail(), $params, 10);

    }

}
