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


    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository, TopicRepository $topicRepository)
    {
        $this->manager= $manager;
        $this->userRepository = $userRepository;
        $this->topicRepository = $topicRepository;
    }

    public function save($userid, $subject)
    {
        $user = $this->userRepository->findOneBy(['id' => $userid]);

        //If subject is user
        if (ctype_digit($subject) == true)   {
            $receiver = $this->userRepository->findOneBy(['id' => $subject]);
            $topic = null;
        }else{
            $topic = $this->topicRepository->findOneBy(['name' => $subject]);
            $receiver = null;
        }

        $notification = new MessageNotification();

        $notification
            ->setUser($user)
            ->setTopic($topic)
            ->setReceiver($receiver)
        ;

        $this->manager->persist($notification);

        $this->manager->flush();
    }
}