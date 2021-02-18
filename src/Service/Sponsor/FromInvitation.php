<?php


namespace App\Service\Sponsor;


use App\Entity\User;
use App\Repository\ScorePointRepository;
use App\Repository\SponsorshipRepository;
use App\Service\Chat\AutomaticMessage;
use App\Service\ScoreHandler;

class FromInvitation
{
    private $scorePointRepository;

    private $automaticMessage;

    private $scoreHandler;

    public function __construct(ScorePointRepository $scorePointRepository, AutomaticMessage  $automaticMessage, ScoreHandler $scoreHandler)
    {
        $this->scorePointRepository = $scorePointRepository;
        $this->automaticMessage = $automaticMessage;
        $this->scoreHandler = $scoreHandler;
    }

    /**
     * Check if user is sponsored and do things
     * @param User $user
     * @return int|null
     */
    public function handle($sponsor, $user)
    {
        $pointsSender = $this->scorePointRepository->findOneBy(['id' => 5])->getPoint();
        $pointsReceiver = $this->scorePointRepository->findOneBy(['id' => 4])->getPoint();

        //Add scores to sender and receiver
        $this->scoreHandler->add($sponsor->getUser(), $pointsSender);
        $this->scoreHandler->add($user, $pointsReceiver);

        // add chat message to sponsor
        $this->automaticMessage->fromAdvisorToSponsored($sponsor, $user);
        $this->automaticMessage->fromAdvisorToSponsor($sponsor, $user);

        return $pointsReceiver;
    }
}