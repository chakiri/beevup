<?php

namespace App\Service;

use App\Entity\CompanyCategory;
use App\Entity\Topic;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class InitTopic
{
    private $user;

    private $manager;

    private $topicRepository;

    public function __construct(Security $security, TopicRepository $topicRepository, EntityManagerInterface $manager)
    {
        $this->user = $security->getUser();
        $this->manager = $manager;
        $this->topicRepository = $topicRepository;
    }


    public function init(CompanyCategory $companyCategory)
    {
        //Verif if topic function exist already
        $topic = $this->topicRepository->findOneBy(['name' => $companyCategory->getSlug(), 'type' => 'category']);
        if (!$topic){
            $topic = new Topic();
            $topic
                ->setName($companyCategory->getSlug())
                ->setType('category')
            ;
            $this->manager->persist($topic);
        }

        //Add this topic to user and delete the other
        $userTopics = $this->user->getTopics();

        foreach ($userTopics as $userTopic){
            if ($userTopic->getType() == 'category'){
                $this->user->removeTopic($userTopic);
            }
        }

        $this->user->addTopic($topic);

        $this->manager->persist($this->user);

        $this->manager->flush();
    }
}