<?php

namespace App\Controller;

use App\Entity\Favorit;
use App\Repository\UserRepository;
use App\Repository\FavoritRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class FavoritController extends AbstractController
{

    /**
     * @Route("/favorit/add/{userId}", name="favorit_add")
     */

     public function addFavorit(Request $request, EntityManagerInterface $manager,UserRepository $userRepo, $userId)
     {
        $user =  $userRepo->findOneBy(['id' => $userId]);
        $favorit =  new Favorit();
        $favorit->setUser($this->getUser())
                ->setFavoritUser($user);
        $manager->persist($favorit);
        $manager->flush();
        $response = new Response(
              'Content',
              Response::HTTP_OK,
              ['content-type' => 'text/html']
              );
              return $response;
     }

         /**
          * @Route("/favorit/delete/{userId}", name="favorit_delete")
          */

        public function deleteFavorit(Request $request, EntityManagerInterface $manager,FavoritRepository $favoritRepo, $userId)
        {

         $favorit =  $favoritRepo->findOneBy(['user' => $this->getUser(), 'favoritUser' => $userId]);
         $manager->remove($favorit);
         $manager->flush();
         $response = new Response(
                       'Content',
                       Response::HTTP_OK,
                       ['content-type' => 'text/html']
                       );
                       return $response;

        }

}