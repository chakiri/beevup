<?php

namespace App\Service\Company;

use App\Repository\UserRepository;
use App\Service\TopicHandler;

class CompanyService
{
    private $userRepository;
    private $topicHandler;

    public function __construct(UserRepository $userRepository, TopicHandler $topicHandler)
    {
       $this->userRepository = $userRepository;
       $this->topicHandler = $topicHandler;
    }

    /**
     * Edit all users store
     * @param $company
     */
    public function updateUsersStore($company): void
    {
       $users =  $this->userRepository->findBy(['company'=>$company->getId()]);
       $store = $company->getStore();
       foreach ($users as $user){
           $user->setStore($store);
           //change general store topic
           $this->topicHandler->initGeneralStoreTopic($user);
       }
  }
}