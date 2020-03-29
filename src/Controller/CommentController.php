<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CommentType;
use App\Repository\RecommandationRepository;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;



class CommentController extends AbstractController
{

   /**
     * @Route("/comment/add", name="comment_create")
     */
    public function create(Request $request, EntityManagerInterface $manager){
      $post = new Comment();
     // $data = $form->getData()
      
   
          
          
          $manager->persist($post);
          $manager->flush();
           return $this->redirectToRoute('dashboard', [
             'id' => $post->getId()
          ]);
    
      return $this->render('post/create.html.twig', [
          'commentForm' => $form->createView(),
         
      ]);
  }
}
