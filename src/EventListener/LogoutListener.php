<?php

namespace App\EventListener;


use App\Repository\OpportunityNotificationRepository;
use App\Repository\UserHistoricRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogoutListener implements LogoutHandlerInterface
{
    private $manager;

    private $userHistoricRepository;

    private $opportunityNotificationRepository;

    public function __construct(EntityManagerInterface $manager, UserHistoricRepository $userHistoricRepository, OpportunityNotificationRepository $opportunityNotificationRepository)
    {
        $this->manager = $manager;
        $this->userHistoricRepository = $userHistoricRepository;
        $this->opportunityNotificationRepository = $opportunityNotificationRepository;
    }

    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        //Get user
        $user = $token->getUser();

        $historic = $this->userHistoricRepository->findOneBy(['user' => $user]);

        $historic->setLastLogout(new \Datetime());

        $this->manager->persist($historic);

        //Set seen to false for notification opportunities
        $opportunityNotification = $this->opportunityNotificationRepository->findOneBy(['user' => $user]);

        if ($opportunityNotification){
            $opportunityNotification->setSeen(false);
            $this->manager->persist($opportunityNotification);
        }

        $this->manager->flush();

    }
}