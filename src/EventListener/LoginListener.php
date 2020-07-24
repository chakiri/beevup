<?php

namespace App\EventListener;


use App\Entity\UserHistoric;
use App\Repository\UserHistoricRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private $manager;

    private $userHistoricRepository;

    public function __construct(EntityManagerInterface $manager, UserHistoricRepository $userHistoricRepository)
    {
        $this->manager = $manager;
        $this->userHistoricRepository = $userHistoricRepository;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        //Get user
        $user = $event->getAuthenticationToken()->getUser();

        $historic = $this->userHistoricRepository->findOneBy(['user' => $user]);

        if (!$historic){
            //New user historic
            $historic = new UserHistoric();

            $historic->setUser($user)
                ->setLastLogin(new \Datetime());
        }else{
            $historic->setLastLogin(new \Datetime());
        }

        $this->manager->persist($historic);

        $this->manager->flush();

    }
}