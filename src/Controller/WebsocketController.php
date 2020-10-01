<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Topic;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\MessageNotificationRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use App\Service\EmptyMessageNotification;
use App\Service\SaveNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebsocketController extends AbstractController
{
    private $userTypeRepo;
    private $userRepo;

    public function __construct(UserTypeRepository $userTypeRepo, UserRepository $userRepo)
    {

        $this->userTypeRepo = $userTypeRepo;
        $this->userRepo = $userRepo;
    }
    /**
     * @Route("/chat/private/{id}", name="chat_private")
     * @Route("/chat/{name}", name="chat_topic")
     */
    public function index(?Topic $topic, ?User $user, Request $request, MessageRepository $messageRepository, MessageNotificationRepository $notificationRepository, EmptyMessageNotification $emptyMessageNotification)
    {
        //Verification passing bad subject to url
        if (!$topic && !$user) return $this->redirectToRoute('page_not_found');

        //If profile is incomplete
        if ($this->getUser()->getProfile()->getIsCompleted() == false){
            $this->addFlash('warning', 'Veuillez completer votre profil pour accéder au chat !');

            return $this->redirectToRoute('profile_edit', [
                'id' => $this->getUser()->getProfile()->getId()
            ]);
        }

        if ($request->get('_route') == 'chat_private'){
            //Empty notification for user
            $emptyMessageNotification->empty($this->getUser(), $user);
            //Assign user to the subject
            $subject = $user->getId();
            //Get all messages from receiver with limit
            $messages = $messageRepository->findMessagesBetweenUserAndReceiver($this->getUser(), $user);

        }elseif($request->get('_route') == 'chat_topic'){
            //if user not having this topic
            if (!in_array($topic, $this->getUser()->getTopics()->toArray())){
                return $this->redirectToRoute('page_not_found');
            }

            //Empty notification for topic
            $emptyMessageNotification->empty($this->getUser(), $topic);
            //Assign topic to the subject
            $subject = $topic->getName();
            //Get all messages from topics with limit
            $messages = $messageRepository->findBy(['topic' => $topic], ['createdAt' => 'ASC']);
        }

        //Get recent users in conversation
        $users = $this->getRecentUsersChat($messageRepository);

        return $this->render('websocket/index3.html.twig', [
            'topics' => $this->getUser()->getTopics(),
            'users' => $users,
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
    public function sender(EntityManagerInterface $manager, TopicRepository $topicRepository, UserRepository $userRepository, MessageNotificationRepository $notificationRepository, MessageRepository $messageRepository, \Swift_Mailer $mailer)
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
            //Get receiver object
            $receiver = $userRepository->findOneBy(['id' => $subject]);

            //Get all messages between user and subject
            $messages = $messageRepository->findMessagesBetweenUserAndReceiver($user, $receiver);
            if (empty($messages)){
                //If no previous messages
                $this->sendEmail($receiver, $mailer);
                var_dump('sent mail');
            }

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
    public function saveNotification(SaveNotification $saveNotification, UserRepository $userRepository, TopicRepository $topicRepository, MessageNotificationRepository $messageNotificationRepository)
    {

        $userid = $_POST['user'];
        $subject = $_POST['subject'];

        $user = $userRepository->find($userid);

        //If subject is user
        if (ctype_digit($subject) == true)   {
            $receiver = $userRepository->findOneBy(['id' => $subject]);
            $notification = $messageNotificationRepository->findOneBy(['user' => $user, 'receiver' => $receiver]);
        }else{
            $topic = $topicRepository->findOneBy(['name' => $subject]);
            $notification = $messageNotificationRepository->findOneBy(['user' => $user, 'topic' => $topic]);
        }

        //Add notification to user
        $saveNotification->save($user, $subject, $notification ? $notification->getNbMessages() : null);

        $response = new Response(json_encode($notification));

        return $response;
    }

    /**
 * Send Email when first private message
 * @param User $user
 * @param \Swift_Mailer $mailer
 */
    private function sendEmail(User $user, \Swift_Mailer $mailer): void
    {
        $userTypePatron = $this->userTypeRepo->findOneBy(['id'=> 4]);
        $storePatron =$this->userRepo->findOneBy(['type'=> $userTypePatron, 'store'=>$user->getStore()]);
        $message = (new \Swift_Message())
            ->setSubject('Beev\'Up par Bureau Vallée | Un autre membre vous a contacté')
            ->setFrom($_ENV['DEFAULT_EMAIL'])
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView('emails/firstMessage.html.twig',  ['user'=> $user,  'storePatron'=> $storePatron]),
                'text/html'
            )
        ;

        $mailer->send($message);
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
}
