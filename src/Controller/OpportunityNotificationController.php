<?php

namespace App\Controller;

use App\Entity\OpportunityNotification;
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

    public function add(EntityManagerInterface $manager, PostRepository $postRepository)
    {
        $notification = new OpportunityNotification();
        $opportunityPosts = $postRepository->findBy(['category'=>'Opportunities'],[]);
        foreach($opportunityPosts as $post)
        {
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