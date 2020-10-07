<?php


namespace App\Service;
use App\Entity\Post;
use App\Repository\PostCategoryRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use Doctrine\ORM\EntityManagerInterface;

class AutmaticPost

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

    public function Add($title, $description, $category, $relatedTo, $relatedToType)
   {
       $adminPLatformeType = $this->userTypeRepository->findOneBy(['id'=>5]);
       $adminPLatformeAccount = $this->userRepository->findOneBy(['type'=>$adminPLatformeType]);
       $post = new Post();
       $post->setUser($adminPLatformeAccount);
       $post->setCategory($category);
       $post->setTitle($title);
       $post->setDescription($description);
       $post->setRelatedTo($relatedTo);
       $post->setRelatedToType($relatedToType);
       $this->manager->persist($post);
       $this->manager->flush();
   }
}