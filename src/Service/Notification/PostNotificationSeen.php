<?php

namespace App\Service\Notification;

use App\Entity\Post;
use App\Repository\PostNotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class PostNotificationSeen
{
    private $manager;

    private $postNotificationRepository;

    public function __construct(EntityManagerInterface $manager, PostNotificationRepository $postNotificationRepository)
    {
        $this->manager = $manager;
        $this->postNotificationRepository = $postNotificationRepository;
    }

    public function set(Post $post)
    {
        $notifications = $this->postNotificationRepository->findBy(['post' => $post]);
        if ($notifications){
            foreach ($notifications as $notification){
                $notification->setSeen(true);
                $this->manager->persist($notification);
            }
        }

        $this->manager->flush();
    }
}