<?php

namespace App\Service\Chat;


use App\Entity\MessageNotification;
use App\Entity\Topic;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SaveNotification
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager= $manager;
    }

    public function save($userid, $subject)
    {
        //If connection to database is interrupted do reconnection
        $this->manager = $this->getEntityManager();

        //Use repository with the restablished entity manager
        $user = $this->manager->getRepository(User::class)->findOneBy(['id' => $userid]);
        //$user = $this->userRepository->findOneBy(['id' => $userid]);

        //If subject is user
        if (ctype_digit($subject) == true || is_int($subject) == true)   {
            //$receiver = $this->userRepository->findOneBy(['id' => $subject]);
            $receiver = $this->manager->getRepository(User::class)->findOneBy(['id' => $subject]);
            $topic = null;
        }else{
            //$topic = $this->topicRepository->findOneBy(['name' => $subject]);
            $topic = $this->manager->getRepository(Topic::class)->findOneBy(['name' => $subject]);
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

    protected function getEntityManager()
    {
        if (false === $this->manager->getConnection()->ping()) {
            $this->manager->getConnection()->close();
            $this->manager->getConnection()->connect();
        }

        return $this->manager;
    }
}