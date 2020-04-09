<?php

namespace App\Service;


use App\Entity\Topic;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class EmptyNotification
{

    private $manager;

    private $notificationRepository;

    public function __construct(EntityManagerInterface $manager, NotificationRepository $notificationRepository)
    {
        $this->manager = $manager;
        $this->notificationRepository= $notificationRepository;
    }

    public function empty($user, $subject)
    {
        if ($subject instanceof Topic){
            $notification = $this->notificationRepository->findOneBy(['user' => $user, 'topic' => $subject]);

        }elseif ($subject instanceof User){
            $notification = $this->notificationRepository->findOneBy(['user' => $user, 'receiver' => $subject]);
        }

        if ($notification){
            $notification->setNbMessages(0);

            $this->manager->persist($notification);
            $this->manager->flush();
        }

    }
}