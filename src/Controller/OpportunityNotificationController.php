<?php

namespace App\Controller;

use App\Entity\OpportunityNotification;
use App\Repository\OpportunityNotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;




class OpportunityNotificationController extends AbstractController
{
    /**
     * @Route("/opportunityNotification/add", name="opportunityNotification_add")
     */

    public function add(EntityManagerInterface $manager, PostRepository $postRepository, OpportunityNotificationRepository $opportunityNotificationRepo)
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
    }
}