<?php
/**
 * Created by PhpStorm.
 * User: mohamedchakiri
 * Date: 22/07/2020
 * Time: 17:02
 */

namespace App\Service;


use App\Repository\OpportunityNotificationRepository;
use App\Repository\PostRepository;
use App\Repository\UserHistoricRepository;
use Symfony\Component\Security\Core\Security;

class LastOpportunities
{
    private $security;

    private $postRepository;

    private $userHistoricRepository;

    private $opportunityNotificationRepository;

    public function __construct(Security $security, PostRepository $postRepository, UserHistoricRepository $userHistoricRepository, OpportunityNotificationRepository $opportunityNotificationRepository)
    {
        $this->security = $security;
        $this->postRepository = $postRepository;
        $this->userHistoricRepository = $userHistoricRepository;
        $this->opportunityNotificationRepository = $opportunityNotificationRepository;
    }

    public function get()
    {
        $historic = $this->userHistoricRepository->findOneBy(['user' => $this->security->getUser()]);

        //Check if opportunities notifications is seen
        $opportunityNotification = $this->opportunityNotificationRepository->findOneBy(['user' => $this->security->getUser()]);

        if ($opportunityNotification  && $opportunityNotification->getSeen() == true)
            $opportunities = $this->postRepository->findOpportunitiesByDate($this->security->getUser(), $opportunityNotification->getLastSeen());
        else{
            if ($historic)
                $opportunities = $this->postRepository->findOpportunitiesByDate($this->security->getUser(), $historic->getLastLogout());
            else
                $opportunities = $this->postRepository->findAll();
        }

        return $opportunities;
    }
}