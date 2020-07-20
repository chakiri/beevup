<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\PostsNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentRepository;
use App\Repository\PostsNotificationRepository;
use Symfony\Component\HttpFoundation\Response;


class CommentController extends AbstractController
{
    /**
     * @Route("/comment/post/{id}", name="comment_post")
     */
    public function add(Post $post, EntityManagerInterface $manager, CommentRepository $commentRepository): Response
    {
        if (isset($_POST['content'])){
            $content = $_POST['content'];

            $comment = new Comment();

            $comment
                ->setUser($this->getUser())
                ->setPost($post)
                ->setDescription($content)
            ;

            $manager->persist($comment);

            //Add notification for the post
            $notification = new PostsNotification();

            $notification
                ->setType('comment')
                ->setSeen(false)
                ->setPost($post)
                ->setUser($this->getUser())
                ->setComment($comment)
            ;

            $manager->persist($notification);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Comment added',
                'idComment' => $comment->getId(),
                'comments' => $commentRepository->count(['post' => $post])
            ], 200);
        }

        return $this->json([
            'code' => 404,
            'message' => 'Comment not found'
        ], 404);
    }

    /**
     * @Route("/comment/{id}/remove", name="remove_comment")
     */
    public function remove(Comment $comment, EntityManagerInterface $manager, PostsNotificationRepository $notificationRepository, CommentRepository $commentRepository): response
    {
        if ($comment->getUser() == $this->getUser()){
            //Find notification
            $notification = $notificationRepository->findOneBy(['user' => $this->getUser(), 'post' => $comment->getPost(), 'comment' => $comment, 'type' => 'comment']);

            $manager->remove($notification);
            $manager->remove($comment);

            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Comment deleted',
                'comments' => $commentRepository->count(['post' => $comment->getPost()])
            ], 200
            );
        }

        return $this->json([
           'code' => 403,
            'message' => 'Not authorized'
            ], 403
        );
    }

   /*{
     * @Route("/comment/add/{variable}",defaults={"variable" = 0}, name="comment_create")
     */
   /*public function create(Request $request, EntityManagerInterface $manager, $variable, PostRepository $PostRepository, CommentRepository $commentRepository)
   {
      $comment = new Comment();
      $dashboardNotification =  new PostsNotification();
      
    
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
   }*/

     /*
     * @Route("/comment/{id}/delete", name="comment_delete")
     */
    /*public function delete(EntityManagerInterface $manager, CommentRepository $commentRepository, PostRepository $postRepository, PostsNotificationRepository $dashboardNotificationRepo, $id)
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

    }*/

}
