<?php

namespace App\Controller;

use App\Entity\OpportunityNotification;
use App\Repository\OpportunityNotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/app")
 */
class OpportunityNotificationController extends AbstractController
{

    /**
     * @Route("/opportunity/notification/set", name="opportunity_notification_set")
     */
    public function set(EntityManagerInterface $manager, OpportunityNotificationRepository $opportunityNotificationRepository)
    {
        $opportunityNotification = $opportunityNotificationRepository->findOneBy(['user' => $this->getUser()]);
        if (!$opportunityNotification){
            $opportunityNotification = new OpportunityNotification();
            $opportunityNotification->setUser($this->getUser());
        }

        $opportunityNotification->setSeen(true);
        $opportunityNotification->setLastSeen(new \Datetime());
        $manager->persist($opportunityNotification);
        $manager->flush();
        return $this->json(['code' => 200,  'message' => 'Notification opportunities set to true'], 200);
    }
}