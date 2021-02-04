<?php

namespace App\EventListener;


use App\Entity\UserHistoric;
use App\Event\Logger\LoggerEntityEvent;
use App\Repository\UserHistoricRepository;
use App\Service\ExpireSubscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private $manager;

    private $userHistoricRepository;

    private $expireSubscription;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EntityManagerInterface $manager, UserHistoricRepository $userHistoricRepository, ExpireSubscription $expireSubscription, EventDispatcherInterface $dispatcher)
    {
        $this->manager = $manager;
        $this->userHistoricRepository = $userHistoricRepository;
        $this->expireSubscription = $expireSubscription;
        $this->dispatcher = $dispatcher;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        //Get user
        $user = $event->getAuthenticationToken()->getUser();

        //Dispatch on Logger Entity Event
        $this->dispatcher->dispatch(new LoggerEntityEvent(LoggerEntityEvent::USER_LOGIN, $user));

        $historic = $this->userHistoricRepository->findOneBy(['user' => $user]);

        if ($user->getCompany()){
            $subscription = $user->getCompany()->getSubscription();
        }

        if (!$historic){
            //New user historic
            $historic = new UserHistoric();

            $historic->setUser($user)
                ->setLastLogin(new \Datetime());
        }else{
            $historic->setLastLogin(new \Datetime());
        }

        //Check expired subscription
        if ($user->getCompany() && $subscription && $this->expireSubscription->expired($subscription) == true) {
            $this->expireSubscription->set($subscription);
        }

        $this->manager->persist($historic);

        $this->manager->flush();
    }
}