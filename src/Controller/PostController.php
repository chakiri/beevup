<?php

namespace App\Controller;

use App\Entity\PostNotification;
use App\Entity\Post;
use App\Entity\PostLike;
use App\Repository\OpportunityNotificationRepository;
use App\Service\ScoreHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PostType;
use App\Repository\CommentRepository;
use App\Repository\PostNotificationRepository;
use App\Repository\PostLikeRepository;
use App\Repository\RecommandationRepository;
use App\Repository\PostRepository;
use App\Repository\AbuseRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;



class PostController extends AbstractController
{

    /**
     * @Route("/post/create", name="post_create")
     */
    public function create(Request $request, EntityManagerInterface $manager, ScoreHandler $scoreHandler){
        $post = new Post();
        $post->setUser($this->getUser());
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $optionsRedirect = [];
            if ($post->getCategory() == 'Opportunité commerciale'){
                $nbPoints = 30;
                $scoreHandler->add($this->getUser(), $nbPoints);
                $optionsRedirect = ['toastScore' => $nbPoints];
            }elseif ($post->getCategory() == 'emploi'){
                $nbPoints = 20;
                $scoreHandler->add($this->getUser(), $nbPoints);
                $optionsRedirect = ['toastScore' => $nbPoints];
            }

            if ($post->getUrlYoutube() != null){
                //Get id video
                $query = parse_url($post->getUrlYoutube(), PHP_URL_QUERY);
                $parameters = str_replace('v=', '', $query);
                $idUrl = explode("&", $parameters, 2);
                $post->setUrlYoutube($idUrl[0]);
            }

            $manager->persist($post);
            $manager->flush();

            $this->addFlash('success', 'Le post a bien été publié !');

            //Get all options redirect in array
            $optionsRedirect = array_merge($optionsRedirect, ['id' => $post->getId()]);

            return $this->redirectToRoute('dashboard', $optionsRedirect);
        }
        return $this->render('post/create.html.twig', [
            'PostForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post/{id}/remove", name="post_remove")
     */
    public function remove(Post $post, EntityManagerInterface $manager, PostLikeRepository $postLikeRepository, CommentRepository $commentRepository, PostNotificationRepository $postsNotificationRepository, AbuseRepository $abuseRepository)
    {
        $postLikes = $postLikeRepository->findBy(['post' => $post]);
        $comments = $commentRepository->findBy(['post' => $post]);
        $postNotifications = $postsNotificationRepository->findBy(['post' => $post]);
        $abuses = $abuseRepository->findBy(['post' => $post]);

        foreach ($postLikes as $like) {
            $manager->remove($like);
        }
        foreach ($abuses as $abuse) {
            $manager->remove($abuse);
        }
        foreach ($postNotifications as $notification) {
            $manager->remove($notification);
        }
        foreach ($comments as $comment) {
            $abuses = $abuseRepository->findBy(['comment' => $comment]);
            if($abuses != null ){
                foreach ($abuses as $abuse) {
                    $manager->remove($abuse);
                }
            }
            $manager->remove($comment);
        }

        $manager->remove($post);

        $manager->flush();

        $this->addFlash('success', 'Votre post a été supprimé avec succès !');

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/post/{id}/likes", name="post_like")
     */
    public function updateLikesNumber(Post $post, EntityManagerInterface $manager, PostLikeRepository $postLikeRepository, PostNotificationRepository $postNotificationRepository): Response
    {
        $user = $this->getUser();

        if ($post->isLikedByUser($user)){
            $like = $postLikeRepository->findOneBy(['user' => $user, 'post' => $post]);

            $notification = $postNotificationRepository->findOneBy(['user' => $user, 'post' => $post, 'type' => 'like']);

            if ($notification) $manager->remove($notification);
            if ($like) $manager->remove($like);

            $manager->flush();

            return $this->json([
                'code'=> 200,
                'message'=> 'like removed',
                'likes' => $postLikeRepository->count(['post' => $post])
            ], 200);

        }else{
            $like = new PostLike();
            $like->setUser($user)
                ->setPost($post)
            ;

            $manager->persist($like);

            $notification = new PostNotification();

            $notification->setPost($post)
                ->setUser($user)
                ->setType('like')
                ->setSeen(false)
            ;

            $manager->persist($notification);

            $manager->flush();

            return $this->json([
                'code'=> 200,
                'message'=> 'like created',
                'likes' => $postLikeRepository->count(['post' => $post])
            ], 200);

        }

    }

    /**
     * @Route("/posts/load-more-posts/{id}", name="load_more_posts")
     */
    public function loadMorePost(PostRepository $postReporsitory, $id){
        $val2 = 1;
        if($id >= 10) {
            $val2 = $id - 10;
        }
        $posts =  $postReporsitory->findByIds($val2, $id);
        return new JsonResponse($posts);
    }

    /**
    * @Route("/post/{id}/edit", name="post_edit")
    */
    public function edit(Post $post, EntityManagerInterface $manager, Request $request)
    {
        
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $manager->persist($post);
            $manager->flush();

           return $this->redirectToRoute('dashboard', [
               
           ]);

        }
        return $this->render('post/create.html.twig', [
            'company' => $post,
            'PostForm' => $form->createView(),
            'post' => $post

        ]);
    }

    /*
     * @Route("/post/{id}/delete", name="post_delete")
     */
    /*public function delete(EntityManagerInterface $manager, PostRepository $postRepository, PostLikeRepository $postLikeRepository, CommentRepository $commentRepository, PostNotificationRepository $dashboardNotificationRepo, AbuseRepository $abuseRepo, OpportunityNotificationRepository $opportunityNotificationRepo, $id)
    {
     $post = $postRepository->findOneByID($id);

     $postLikes = $postLikeRepository->findByPost($post);
     $comments = $commentRepository->findByPost($post);
     $dashboardNotifications = $dashboardNotificationRepo->findByPost($post);
     $opportunityNotif = $opportunityNotificationRepo->findBy(['post'=>$post],[]);
     $abuses = $abuseRepo->findByPost($post);
     
     foreach ($postLikes as $object) {
        $manager->remove($object);
     }
     foreach ($abuses as $object) {
        $manager->remove($object);
     }

      foreach ($opportunityNotif as $object) {
          $manager->remove($object);
      }
     foreach ($comments as $object) {
        $abuses = $abuseRepo->findByComment($object);
        if($abuses !=null ){
        foreach ($abuses as $obj) {
        $manager->remove($obj);
        }
    }
        $manager->remove($object);
     }
     foreach ($dashboardNotifications as $object) {
        $manager->remove($object);
     }
     
     $manager->remove($post);
     $manager->flush();
     $response = new Response(
        'Content',
        Response::HTTP_OK,
        ['content-type' => 'text/html']
    );
    return $response;

    }*/

}
