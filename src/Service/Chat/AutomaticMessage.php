<?php


namespace App\Service\Chat;


use App\Entity\Sponsorship;
use App\Entity\User;
use App\Repository\ScorePointRepository;
use App\Repository\UserRepository;
use App\Service\SaveNotification;

class AutomaticMessage
{
    private $userRepository;
    private $saveMessage;
    private $saveNotification;
    private $scorePointRepository;

    public function __construct(UserRepository $userRepository, SaveMessage $saveMessage, SaveNotification $saveNotification, ScorePointRepository $scorePointRepository)
    {
        $this->userRepository = $userRepository;
        $this->saveMessage = $saveMessage;
        $this->saveNotification =$saveNotification;
        $this->scorePointRepository = $scorePointRepository;
    }

    public function fromAdvisorToSponsored(Sponsorship $sponsor, User $sponsored): void
    {
        $content = "Bravo vous venez de vous inscrire grâce au parrainage de " . $sponsor->getUser()->getProfile()->getFirstname() . " " . $sponsor->getUser()->getProfile()->getLastname() . " de la société " . $sponsor->getUser()->getCompany();

        $this->fromAdvisorToUser($sponsored, $content);

    }

    public function fromAdvisorToSponsor(Sponsorship $sponsor, User $sponsored): void
    {
        $user = $sponsor->getUser();

        $points = $this->scorePointRepository->findOneBy(['id' => 5])->getPoint();

        $content = "Merci grâce à vous, la société " . $sponsored->getCompany() . " vient de rejoindre la communauté Beev’Up et vous avez gagné " . $points ." points";

        $this->fromAdvisorToUser($user, $content);

    }

    public function fromAdvisorToUser(User $user, string $content): void
    {
        //Get advisor of store
        $advisor = $this->userRepository->findOneBy(['id'=> $user->getStore()->getDefaultAdviser()]);

        //Save message
        $this->saveMessage->save($advisor->getId(), $content, true, $user->getId());

        //Save Notification
        $this->saveNotification->save($user->getId(), $advisor->getId());

    }
}