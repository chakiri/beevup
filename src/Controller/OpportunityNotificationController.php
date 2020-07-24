<?php

namespace App\Controller;

use App\Entity\OpportunityNotification;
use App\Repository\OpportunityNotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;



class OpportunityNotificationController extends AbstractController
{
    /**
     * @Route("/opportunityNotification/add", name="opportunityNotification_add")
     */
    /*public function add(EntityManagerInterface $manager, PostRepository $postRepository, OpportunityNotificationRepository $opportunityNotificationRepo)
    {
        $OpportunityPostsIds = [''];
        $displayedOpportunityPosts =$opportunityNotificationRepo->findByLastMonthNotification($this->getUser());

        foreach ($displayedOpportunityPosts as $post ) {
            array_push($OpportunityPostsIds , $post->getPost()->getId());
        }
        $opportunityPosts = $postRepository->findByNotSeenOpportunityPost("Opportunité commerciale", $OpportunityPostsIds, $this->getUser());
        foreach($opportunityPosts as $post)
        {
            $notification = new OpportunityNotification();
            $notification->setUser($this->getUser());
            $notification->setPost($post);
            $notification->setSeen(1);
            $manager->persist($notification);
            $manager->flush();
        }
        $response = new Response(
            'Content',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
        return $response;
    }*/

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

        return $this->json([
            'code' => 200,
            'message' => 'Notification opportunities set to true'
        ], 200);
    }
}