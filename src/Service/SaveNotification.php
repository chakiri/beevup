<?php

namespace App\Service;


use App\Entity\Notification;
use App\Entity\Topic;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class SaveNotification
{
    private $manager;

    private $notificationRepository;

    private $userRepository;

    private $topicRepository;


    public function __construct(EntityManagerInterface $manager, NotificationRepository $notificationRepository, UserRepository $userRepository, TopicRepository $topicRepository)
    {
        $this->manager= $manager;
        $this->notificationRepository = $notificationRepository;
        $this->userRepository = $userRepository;
        $this->topicRepository = $topicRepository;
    }

    public function save($userid, $subject)
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
            $notification = new Notification();

            $notification
                ->setUser($user)
                ->setTopic($topic)
                ->setReceiver($receiver)
                ->setNbMessages(1)
            ;

            $this->manager->persist($notification);
        }else{
            $nbMessages = $notification->getNbMessages();
            $nbMessages++;

            $notification->setNbMessages($nbMessages);
        }

        $this->manager->persist($notification);

        $this->manager->flush();
    }
}