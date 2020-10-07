<?php

namespace App\Service;


use App\Entity\MessageNotification;
use App\Entity\User;
use App\Repository\MessageNotificationRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class SaveNotification
{
    private $manager;

    private $userRepository;

    private $topicRepository;

    private $messageNotificationRepository;


    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository, TopicRepository $topicRepository, MessageNotificationRepository $messageNotificationRepository)
    {
        $this->manager= $manager;
        $this->userRepository = $userRepository;
        $this->topicRepository = $topicRepository;
        $this->messageNotificationRepository = $messageNotificationRepository;
    }

    public function getNotification($user, $subject)
    {
        if (!$user instanceof User){
            $user = $this->userRepository->findOneBy(['id' => $user]);
        }
        //If subject is user
        if (ctype_digit($subject) == true)   {
            $receiver = $this->userRepository->findOneBy(['id' => $subject]);
            $notification = $this->messageNotificationRepository->findOneBy(['user' => $user, 'receiver' => $receiver]);
        }else{
            $topic = $this->topicRepository->findOneBy(['name' => $subject]);
            $notification = $this->messageNotificationRepository->findOneBy(['user' => $user, 'topic' => $topic]);
        }

        return $notification;
    }

    public function save($userid, $subject, $nbNotifications)
    {
        $user = $this->userRepository->find($userid);

        $notification = $this->getNotification($user, $subject);

        //If subject is user
        if (ctype_digit($subject) == true) $topic = null;
        else $receiver = null;

        if (!$notification){
            $notification = new MessageNotification();

            $notification
                ->setUser($user)
                ->setTopic($topic)
                ->setReceiver($receiver)
                ->setNbMessages(1)
            ;

            $this->manager->persist($notification);
        }else{
            $notification->setNbMessages($nbNotifications + 1);

            $this->manager->persist($notification);
        }

        $this->manager->flush();
    }
}