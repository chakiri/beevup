<?php

namespace App\Service;


use App\Entity\Topic;
use App\Entity\User;
use App\Repository\MessageNotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class EmptyMessageNotification
{
    private $manager;

    private $messageNotificationRepository;

    public function __construct(EntityManagerInterface $manager, MessageNotificationRepository $messageNotificationRepository)
    {
        $this->manager = $manager;
        $this->messageNotificationRepository= $messageNotificationRepository;
    }

    public function empty($user, $subject)
    {
        if ($subject instanceof Topic){
            $notifications = $this->messageNotificationRepository->findBy(['user' => $user, 'topic' => $subject]);

        }elseif ($subject instanceof User){
            $notifications = $this->messageNotificationRepository->findBy(['user' => $user, 'receiver' => $subject]);
        }

        if ($notifications){
            foreach ($notifications as $notification){
                $this->manager->remove($notification);
            }
            $this->manager->flush();
        }

    }
}