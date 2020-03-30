<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PostType;
use App\Repository\RecommandationRepository;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;



class PostController extends AbstractController
{

   /**
     * @Route("/post/create", name="post_create")
     */
    public function create(Request $request, EntityManagerInterface $manager){
      $post = new Post();
      $post->setUser($this->getUser());
      $form = $this->createForm(PostType::class, $post);
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
          
          
          $manager->persist($post);
          $manager->flush();
          $this->addFlash('create-post-success', 'Le post a bien été publié. !');
          return $this->redirectToRoute('dashboard', [
             'id' => $post->getId()
          ]);
      }
      return $this->render('post/create.html.twig', [
          'PostForm' => $form->createView(),
         
      ]);
  }



    /**
    * @Route("/post", name="post")
    */

    public function index()
    {
        return $this->render('post/show.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    /**
    * @Route("/post/{slug}", name="post_show")
    */

    public function show()
    {
        return $this->render('post/show.html.twig', [
            
        ]);
    }

    /**
    * @Route("/post/{id}/edit", name="post_edit")
    */

    public function edit(Post $post, EntityManagerInterface $manager, Request $request, PostRepository $repository)
    {
        
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $manager->persist($post);
            $manager->flush();

           return $this->redirectToRoute('post_show', [
               
           ]);

        }
        return $this->render('post/edit.html.twig', [
            'company' => $post,
            'EditPostorm' => $form->createView(),
        ]);
    }
    
    /**
    * @Route("/post/{id}/update-post-likes", name="post_update_likes_number")
    */
    public function updateLikesNumber(Post $post, EntityManagerInterface $manager, Request $request, PostRepository $repository)
    {
        $likesNumber = $post->getLikesNumber() + 1 ;
        $post->setLikesNumber($likesNumber);
        $manager->persist($post);
        $manager->flush();
        $response = new Response(
            'Content',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
        return $response;
        
    }



}
