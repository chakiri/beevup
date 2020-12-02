<?php

namespace App\Service\Chat;



use App\Controller\WebsocketController;
use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\ProfilRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class SaveMessage
{
    protected $topicRepository;
    protected $userRepository;
    protected $messageRepository;
    protected $manager;
    protected $websocketController;
    protected $profilRepository;
    protected $mailer;

    public function __construct(EntityManagerInterface $manager, TopicRepository $topicRepository, UserRepository $userRepository, MessageRepository $messageRepository, WebsocketController $websocketController, ProfilRepository $profilRepository, \Swift_Mailer $mailer)
    {
        $this->topicRepository = $topicRepository;
        $this->userRepository = $userRepository;
        $this->messageRepository = $messageRepository;
        $this->manager = $manager;
        $this->websocketController = $websocketController;
        $this->profilRepository = $profilRepository;
        $this->mailer = $mailer;
    }

    public function save($idUser, $content, $isPrivate, $subject)
    {
        //Save data in DB
        $message = new Message();

        //Get user from idUser
        $user = $this->userRepository->findOneBy(['id' => $idUser]);

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

            $message
                ->setReceiver($receiver)
                ->setIsPrivate($isPrivate)
            ;
        }

        $this->manager->persist($message);
        $this->manager->flush();
    }

}