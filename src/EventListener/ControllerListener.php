<?php

namespace App\EventListener;

use App\Repository\MessageNotificationRepository;
use App\Repository\PostNotificationRepository;
use App\Repository\PostRepository;
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

    public function __construct(Security $security, Environment $twig, PostRepository $postRepository, PostNotificationRepository $postNotificationRepository, MessageNotificationRepository $messageNotificationRepository, LastOpportunities $lastOpportunities)
    {
        $this->security = $security;
        $this->twig = $twig;
        $this->postRepository = $postRepository;
        $this->postNotificationRepository = $postNotificationRepository;
        $this->messageNotificationRepository = $messageNotificationRepository;
        $this->lastOpportunities = $lastOpportunities;
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
            $messagesNotification = $this->messageNotificationRepository->findBy(['receiver' => $user]);

            //Get last opportunities
            $opportunities = $this->lastOpportunities->get();

            //Save in Twig global variable
            $this->twig->addGlobal('postsNotifications', $postsNotifications);
            $this->twig->addGlobal('messagesNotifications', $messagesNotification);
            $this->twig->addGlobal('opportunities', $opportunities);
        }

    }
}