<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\PostLike;
use App\Repository\PostLikeRepository;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PostLikesController extends AbstractController
{
      /**
    * @Route("/Likes/{id}", name="post_likes")
    */
    public function show(PostLikeRepository $postLikesRepository, PostRepository $postReporsitory, $id)
    {
        $post = $postReporsitory->findOneBy(['id' => $id]);
        $postLikes = $postLikesRepository->findBy(['post' => $post->getId()], []);
        return $this->render('postLikes/show.html.twig', [  'postLikes' => $postLikes]);
    }
}