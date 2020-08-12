<?php

namespace App\EventListener;


use App\Entity\UserHistoric;
use App\Repository\UserHistoricRepository;
use App\Service\ExpireSubscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private $manager;

    private $userHistoricRepository;

    private $expireSubscription;

    public function __construct(EntityManagerInterface $manager, UserHistoricRepository $userHistoricRepository, ExpireSubscription $expireSubscription)
    {
        $this->manager = $manager;
        $this->userHistoricRepository = $userHistoricRepository;
        $this->expireSubscription = $expireSubscription;
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

        //Check expired subscription
        if ($user->getCompany() && $user->getCompany()->getSubscription())
            $this->expireSubscription->check($user->getCompany()->getSubscription());

        $this->manager->persist($historic);

        $this->manager->flush();

    }
}