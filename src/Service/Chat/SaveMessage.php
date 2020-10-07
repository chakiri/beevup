<?php

namespace App\Service\Chat;



use App\Controller\WebsocketController;
use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\ProfilRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class SaveMessage
{
    protected $topicRepository;
    protected $userRepository;
    protected $messageRepository;
    protected $mailer;
    protected $manager;
    protected $websocketController;
    protected $profilRepository;

    public function __construct(EntityManagerInterface $manager, TopicRepository $topicRepository, UserRepository $userRepository, MessageRepository $messageRepository, \Swift_Mailer $mailer, WebsocketController $websocketController, ProfilRepository $profilRepository)
    {
        $this->topicRepository = $topicRepository;
        $this->userRepository = $userRepository;
        $this->messageRepository = $messageRepository;
        $this->mailer = $mailer;
        $this->manager = $manager;
        $this->websocketController = $websocketController;
        $this->profilRepository = $profilRepository;
    }

    public function save($name, $content, $isPrivate, $subject)
    {
        //Save data in DB
        $message = new Message();

        //Get user from firstname
        $profile = $this->profilRepository->findOneBy(['firstname' => $name]);
        $user = $profile->getUser();

        $message
            ->setUser($user)
            ->setContent($content)
            ->setCreatedAt(new \DateTime())
        ;

        if ($isPrivate == false){
            //Get topic object
            $topic = $this->topicRepository->findOneBy(['name' => $subject]);

            $message
                ->setTopic($topic)
                ->setIsPrivate($isPrivate)
            ;

        }else {
            //Get receiver object
            $receiver = $this->userRepository->findOneBy(['id' => $subject]);

            //Get all messages between user and subject
            $messages = $this->messageRepository->findMessagesBetweenUserAndReceiver($user, $receiver);
            if (empty($messages)){
                //If no previous messages
                $this->websocketController->sendEmail($receiver, $this->mailer);
                var_dump('sent mail');
            }

            $message
                ->setReceiver($receiver)
                ->setIsPrivate($isPrivate)
            ;
        }

        $this->manager->persist($message);
        $this->manager->flush();
    }
}