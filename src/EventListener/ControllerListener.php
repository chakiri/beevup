<?php

namespace App\EventListener;

use App\Repository\MessageNotificationRepository;
use App\Repository\PostNotificationRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\LastOpportunities;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class ControllerListener {

    private $security;

    private $twig;

    private $postRepository;

    private $postNotificationRepository;

    private $messageNotificationRepository;

    private $lastOpportunities;

    private $userRepository;

    public function __construct(Security $security, Environment $twig, PostRepository $postRepository, PostNotificationRepository $postNotificationRepository, MessageNotificationRepository $messageNotificationRepository, LastOpportunities $lastOpportunities, UserRepository $userRepository)
    {
        $this->security = $security;
        $this->twig = $twig;
        $this->postRepository = $postRepository;
        $this->postNotificationRepository = $postNotificationRepository;
        $this->messageNotificationRepository = $messageNotificationRepository;
        $this->lastOpportunities = $lastOpportunities;
        $this->userRepository = $userRepository;
    }

    public function onKernelController()
    {
        $user = $this->security->getUser();

        if ($user){
            //Get posts of user
            $posts = $this->postRepository->findBy(['user' => $user]);

            $postsNotifications = [];
            //Get notification for each post
            foreach ($posts as $post){
                $postNotifications = $this->postNotificationRepository->findByOtherUser($post, $user);
                $postsNotifications = array_merge($postsNotifications, $postNotifications);
            }

            //Get messages notification
            $notifications = $this->messageNotificationRepository->findBy(['user' => $user]);
            $messagesNotification = [];
            foreach ($notifications as $notification){
                if ($notification->getReceiver() != null ){
                    if (!array_key_exists($notification->getReceiver()->getId(), $messagesNotification)){
                        $messagesNotification[$notification->getReceiver()->getId()] = 0;
                    }
                    $messagesNotification[$notification->getReceiver()->getId()]++;
                }elseif ($notification->getTopic() != null ){
                    if (!array_key_exists($notification->getTopic()->getName(), $messagesNotification)){
                        $messagesNotification[$notification->getTopic()->getName()] = 0;
                    }
                    $messagesNotification[$notification->getTopic()->getName()]++;
                }
            }

            //Get last opportunities
            $opportunities = $this->lastOpportunities->get();

            //Get All users
            $users = $this->userRepository->findAll();

            //Save in Twig global variable
            $this->twig->addGlobal('postsNotifications', $postsNotifications);
            $this->twig->addGlobal('messagesNotifications', $messagesNotification);
            $this->twig->addGlobal('users', $users);
            $this->twig->addGlobal('opportunities', $opportunities);
        }

    }
}