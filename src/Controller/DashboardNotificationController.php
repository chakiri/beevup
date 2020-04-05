<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\DashboardNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Response;

class DashboardNotificationController extends AbstractController
{

   /**
    * @Route("/updateNotifications", name="update_notifications")
    */
    public function updateLikesNumber(EntityManagerInterface $manager, Request $request)
    {
      
      $qb = $manager->createQueryBuilder();
      $q = $qb->update(DashboardNotification::class, 'u')
              ->set('u.seen', 1)
              ->where('u.owner = ?1')
              ->setParameter('1', $this->getUser())
              ->getQuery();
      $p = $q->execute();
        $response = new Response(
            'Content',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
        return $response;
        
    }

}