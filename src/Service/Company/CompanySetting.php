<?php

namespace App\Service\Company;

use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use App\Service\TopicHandler;
use Doctrine\ORM\EntityManagerInterface;

class CompanySetting
{
    private $userRepository;
    private $topicHandler;
    private $manager;
    private CompanyRepository $companyRepository;

    public function __construct(UserRepository $userRepository, TopicHandler $topicHandler, EntityManagerInterface $manager, CompanyRepository $companyRepository)
    {
        $this->userRepository = $userRepository;
        $this->topicHandler = $topicHandler;
        $this->manager = $manager;
        $this->companyRepository = $companyRepository;
    }

    /**
     * Edit store and general topic to all users of the company
     * @param $company
     */
    public function updateUsersStore($company): void
    {
        $users =  $this->userRepository->findBy(['company'=>$company->getId()]);
        $store = $company->getStore();
        foreach ($users as $user){
           $user->setStore($store);
           $this->manager->persist($user);
           //change general store topic
           $this->topicHandler->initGeneralStoreTopic($user);
        }

        $this->manager->flush();
   }

}