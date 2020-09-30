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
            $notification = $this->messageNotificationRepository->findOneBy(['user' => $user, 'topic' => $subject]);

        }elseif ($subject instanceof User){
            $notification = $this->messageNotificationRepository->findOneBy(['user' => $user, 'receiver' => $subject]);
        }

        if ($notification){
            $notification->setNbMessages(null);

            $this->manager->persist($notification);
            $this->manager->flush();
        }

    }
}