<?php

namespace App\Controller;

use App\Entity\Abuse;
use App\Form\AbuseType;
use App\Repository\AbuseRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class AbuseController extends AbstractController
{

    /**
    * @Route("/abuse/add/{postId}/{commentId}", name="abuse_add")
    */
   
   public function addAbuse(Request $request, EntityManagerInterface $manager, $postId, $commentId  )
   {
    $abuse = new Abuse();
    $form = $this->createForm(AbuseType::class, $abuse);
    return $this->render('abuse/add.html.twig', [
      'AbuseForm' => $form->createView(),
      'postId'=>$postId,
      'commentId' => $commentId
     ]);
    
   }  
    /**
    * @Route("/abuse/insert/{val1}/{val2}", name="abuse_insert")
    * $val1 is the post id
    * $val2 is the commentId
    */

    public function insertAbuse(Request $request, EntityManagerInterface $manager,PostRepository $postRepo, CommentRepository $commentRepo, $val1, $val2)
    {
     
     $abuse = new Abuse();
     $description = $request->request->get('description');
     $user  = $this->getUser();
     if($val1 !=0)
     {
         $post = $postRepo->findOneBy(['id' => $val1]);
         $abuse->setPost( $post)
               ->setType('Post')
               ->setDescription($description)
               ->setUser($user)
               ->setStatus(0);
     }
     if($val2 !=0)
     {
          // this abuse is related to comment
          $comment = $commentRepo->findOneBy(['id' => $val2]);
          $abuse->setComment( $comment)
          ->setType('Comment')
          ->setDescription($description)
          ->setUser($user)
          ->setStatus(0);
     }
     $manager->persist($abuse);
     $manager->flush();
     $response = new Response(
      'Content',
      Response::HTTP_OK,
      ['content-type' => 'text/html']
      );
      return $response;
   
     }

    /**
    * @Route("/abuses", name="abuses")
    */

    public function index(AbuseRepository $repository)
    {
        $abuses = $repository->findBy(['status' => 0],  array('createdAt' => 'DESC'));
        
        return $this->render('abuse/index.html.twig', [
            'abuses' => $abuses
        ]);
    }
    
     /**
     * @Route("/edit/abuse/{variable}/{variable2}", defaults={"variable" = 0, "variable2" = 0}, name="abuse_edit")
     */

    public function edit(Request $request, EntityManagerInterface $manager, AbuseRepository $repository,PostRepository $postRepository, CommentRepository $commentRepository, $variable, $variable2)
    {
        $abuse = $repository->findOneById($variable2);
       
        $status = (1 == $variable) ? 1 : 2;
        $abuse->setStatus($status);
        $manager->persist($abuse);
        if($abuse->getPost() != null)
        {
          $post = $postRepository->findOneById($abuse->getPost()->getId());
            if($status == 1) {
              $post->setStatus(1);
            } else {
              $post->setStatus(0);
            }
          $manager->persist($post);
        }
        if($abuse->getComment() != null){
          $comment = $commentRepository->findOneById($abuse->getComment()->getId());
          $post = $postRepository->findOneById($comment->getPost()->getId());
          if($status == 1) {
            $comment->setStatus(1);
            $post->setCommentsNumber($post->getCommentsNumber() - 1);
            $manager->persist($post);
          } else {
            $comment->setStatus(0);
          }
          $manager->persist($comment);
        }
        $manager->flush();
        $response = new Response(
            'Content',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
        return $response;
    }

}