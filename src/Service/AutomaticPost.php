<?php


namespace App\Service;
use App\Entity\Post;
use App\Repository\PostCategoryRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use Doctrine\ORM\EntityManagerInterface;

class AutomaticPost

{
    private $postCategoryRepository;
    private $manager;
    private $userRepository;
    private $userTypeRepository;

    public function __construct(PostCategoryRepository $postCategoryRepository, UserRepository $userRepository, UserTypeRepository $userTypeRepository, EntityManagerInterface $manager)
    {
        $this->postCategoryRepository = $postCategoryRepository;
        $this->manager = $manager;
        $this->userTypeRepository = $userTypeRepository;
        $this->userRepository =$userRepository;
    }

    public function Add($user, $title, $description, $category, $relatedTo, $relatedToType, $recommandation = null)
   {
       $adminPLatformeType = $this->userTypeRepository->findOneBy(['id'=>5]);
       $adminPLatformeAccount = $this->userRepository->findOneBy(['type'=>$adminPLatformeType]);
       $post = new Post();
     //  $post->setUser($adminPLatformeAccount);
       $post->setUser($user);
       $post->setCategory($category);
       $post->setTitle($title);
       $post->setDescription($description);
       $post->setRelatedTo($relatedTo);
       $post->setRelatedToType($relatedToType);
       if($recommandation != null){
         $post->setRelatedToRecommandation($recommandation);
        }
       $this->manager->persist($post);
       $this->manager->flush();
   }

   public function  generateTitle($service){
   $title = '';

    if($service->getUser()->getCompany() != null) {
         $title = 'L\'entreprise '.$service->getUser()->getCompany()->getName() . ' vous propose le service ' . $service->getTitle();
    } else {
         $title = 'Le magasin '.$service->getUser()->getStore()->getName() . ' vous propose le service ' . $service->getTitle();
    }
    return $title;
   }


}