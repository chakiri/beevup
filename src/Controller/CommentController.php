<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\PostNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentRepository;
use App\Repository\PostNotificationRepository;
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
            $notification = new PostNotification();

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
    public function remove(Comment $comment, EntityManagerInterface $manager, PostNotificationRepository $postNotificationRepository, CommentRepository $commentRepository): response
    {
        if ($comment->getUser() == $this->getUser()){
            //Find notification
            $notification = $postNotificationRepository->findOneBy(['user' => $this->getUser(), 'post' => $comment->getPost(), 'comment' => $comment, 'type' => 'comment']);

            if ($notification) $manager->remove($notification);
            if ($comment) $manager->remove($comment);

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

}
