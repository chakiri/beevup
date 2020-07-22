<?php

namespace App\EventListener;


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

    public function __construct(EntityManagerInterface $manager, UserHistoricRepository $userHistoricRepository)
    {
        $this->manager = $manager;
        $this->userHistoricRepository = $userHistoricRepository;
    }

    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        //Get user
        $user = $token->getUser();

        $historic = $this->userHistoricRepository->findOneBy(['user' => $user]);

        $historic->setLastLogout(new \Datetime());

        $this->manager->persist($historic);

        $this->manager->flush();

    }
}