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
use App\Repository\DashboardNotificationRepository;
use Symfony\Component\HttpFoundation\Response;




class CommentController extends AbstractController
{

   /**
     * @Route("/comment/add/{variable}",defaults={"variable" = 0}, name="comment_create")
     */
    public function create(Request $request, EntityManagerInterface $manager, $variable, PostRepository $PostRepository, CommentRepository $commentRepository){
      $comment = new Comment();
      $dashboardNotification =  new DashboardNotification();
      
    
      $commentDescription = $request->request->get('comment');
      $comment->setDescription($commentDescription);
      $post = $PostRepository->findOneById($variable);
      $userComments = $commentRepository->findPostAndUser($this->getUser(), $post);
      $post->setCommentsNumber($post->getCommentsNumber()+ 1);
      if(count($userComments) == 0) {
        $post->setDistinctUserCommentNumber($post->getDistinctUserCommentNumber()+ 1);
      }
     
      $comment->setPost($post);
      
      $comment->setUser($this->getUser());
      $dashboardNotification->setType('comment');
      $dashboardNotification->setSeen(0);
      $dashboardNotification->setPost($post);
      $dashboardNotification->setUser($this->getUser());
      $dashboardNotification->setOwner($post->getUser());
      $dashboardNotification->setComment($comment);
      $manager->persist($comment);
      $manager->persist($post);
      $manager->persist($dashboardNotification);
      $manager->flush();
      $id = $comment->getId();
      $response = new Response(
              $id,
              Response::HTTP_OK,
              ['content-type' => 'text/html']
          );
          return $response;
 }
     /**
     * @Route("/comment/{id}/delete", name="comment_delete")
     */

    public function delete(Request $request, EntityManagerInterface $manager, CommentRepository $commentRepository, PostRepository $postRepository,DashboardNotificationRepository $dashboardNotificationRepo, $id)
    {
     $comment = $commentRepository->findOneByID($id);
     $postId = $comment->getPost()->getId();
     $post = $postRepository->findOneByID($postId);
     $post->setCommentsNumber($post->getCommentsNumber()-1);
     $dashboardNotification = $dashboardNotificationRepo->findOneByComment($comment);
     $manager->remove($comment);
     $manager->remove($dashboardNotification);
     $manager->persist($post);
     $manager->flush();
     $response = new Response(
        'Content',
        Response::HTTP_OK,
        ['content-type' => 'text/html']
    );
    return $response;

    }

    /**
    * @Route("/comment/{id}/update-comment/{variable}", name="comment_update")
    */
    public function updateLikesNumber(Comment $comment, EntityManagerInterface $manager, Request $request, CommentRepository $repository, $variable)
    {
      $comment->setDescription($variable);
      $manager->persist($comment);
      $manager->flush();
      $response = new Response(
        'Content',
        Response::HTTP_OK,
        ['content-type' => 'text/html']
    );
    return $response;
    }


}
