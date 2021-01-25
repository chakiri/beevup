<?php

namespace App\Service;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;

class AutomaticPost
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

   public function Add($user, $title, $description, $category, $relatedTo, $relatedToType, $recommandation = null, $toCompany = null)
   {
       $post = new Post();
       $post->setUser($user);
       $post->setCategory($category);
       $post->setTitle($title);
       $post->setDescription($description);
       $post->setRelatedTo($relatedTo);
       $post->setRelatedToType($relatedToType);
       if( ! is_null($toCompany)){
       $post->setToCompany($toCompany);
       }
       if( ! is_null($recommandation) ){
         $post->setRelatedToRecommandation($recommandation);
        }
       $this->manager->persist($post);
       $this->manager->flush();
   }

   public function  generateTitle($service)
   {
        if($service->getUser()->getCompany() != null) {
             $title = 'L\'entreprise '.$service->getUser()->getCompany()->getName() . ' vous propose le service ' . $service->getTitle();
        } else {
             $title = 'Le magasin '.$service->getUser()->getStore()->getName() . ' vous propose le service ' . $service->getTitle();
        }
        return $title;
   }


}