<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\CompanyCategory;
use App\Entity\Store;
use App\Entity\Topic;
use App\Entity\TopicType;
use App\Entity\User;
use App\Repository\TopicRepository;
use App\Repository\TopicTypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class TopicHandler
{
    private $user;

    private $manager;

    private $topicRepository;

    private $topicTypeRepository;

    private $userRepository;

    public function __construct(Security $security, TopicRepository $topicRepository, TopicTypeRepository $topicTypeRepository, UserRepository $userRepository, EntityManagerInterface $manager)
    {
        $this->user = $security->getUser();
        $this->manager = $manager;
        $this->topicRepository = $topicRepository;
        $this->topicTypeRepository = $topicTypeRepository;
        $this->userRepository = $userRepository;
    }


    public function addAdminTopicsToUser(User $user): void
    {
        $type = $this->topicTypeRepository->findOneBy(['name' => 'admin']);

        //Get all admin topics
        $topics = $this->topicRepository->findBy(['type' => $type]);

        foreach ($topics as $topic){
            $user->addTopic($topic);
        }
    }

    public function initCategoryCompanyTopic(CompanyCategory $companyCategory, ?User $user = null): void
    {
        $this->init('categoryCompany', $companyCategory->getSlug(), $user);
    }

    public function initCompanyTopic(Company $company, User $user): void
    {
        $this->init('company', $company->getSlug(), $user);
    }

    public function initAdminStoreTopic(User $user): void
    {
        $this->init('admin_store', 'franchisé', $user);
    }

    public function initStoreTopic(Store $store, User $user): void
    {
        $this->init('function', $store->getSlug(), $user);
    }

    public function initFunctionStoreTopic(User $user): void
    {
        $this->init('store', $user->getProfile()->getFunction()->getSlug(), $user);
    }

    protected function init(String $typeName, String $topicName, ?User $user = null): void
    {
        //Get type topic
        $type = $this->topicTypeRepository->findOneBy(['name' => $typeName]);
        if (!$type){
            $type = new TopicType();
            $type->setName($typeName);

            $this->manager->persist($type);
        }
        //Get Topic
        $topic = $this->topicRepository->findOneBy(['name' => $topicName, 'type' => $type]);
        if (!$topic){
            $topic = new Topic();
            $topic
                ->setName($topicName)
                ->setType($type)
            ;
            $this->manager->persist($topic);
        }

        //Get user
        if (!$user){
            $user = $this->user;
        }

        //Add topic to user
        $userTopics = $user->getTopics();

        foreach ($userTopics as $userTopic){
            if ($userTopic->getType() == $type){
                $user->removeTopic($userTopic);
            }
        }
        $user->addTopic($topic);

        $this->manager->persist($user);

        $this->manager->flush();
    }

    public function getUsersByTopic($topicName)
    {
        return $this->userRepository->findByTopic($topicName);
    }
}