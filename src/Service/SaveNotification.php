<?php

namespace App\Service;


use App\Entity\MessageNotification;
use App\Repository\MessageNotificationRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class SaveNotification
{
    private $manager;

    private $notificationRepository;

    private $userRepository;

    private $topicRepository;


    public function __construct(EntityManagerInterface $manager, MessageNotificationRepository $notificationRepository, UserRepository $userRepository, TopicRepository $topicRepository)
    {
        $this->manager= $manager;
        $this->notificationRepository = $notificationRepository;
        $this->userRepository = $userRepository;
        $this->topicRepository = $topicRepository;
    }

    public function save($userid, $subject, $nbNotifications)
    {
        $user = $this->userRepository->find($userid);

        //If subject is user
        if (ctype_digit($subject) == true)   {
            $receiver = $this->userRepository->findOneBy(['id' => $subject]);
            $notification = $this->notificationRepository->findOneBy(['user' => $user, 'receiver' => $receiver]);
            $topic = null;
        }else{
            //Find subject
            $topic = $this->topicRepository->findOneBy(['name' => $subject]);
            $notification = $this->notificationRepository->findOneBy(['user' => $user, 'topic' => $topic]);
            $receiver = null;
        }

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
            //Problem nbNotif not update
            /*if ($nbNotifications != null) $nbMessages = $nbNotifications;
            else $nbMessages = $notification->getNbMessages();*/

            $notification->setNbMessages($nbNotifications + 1);

            $this->manager->persist($notification);
        }

        $this->manager->flush();
    }
}