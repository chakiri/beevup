<?php

namespace App\Service;


use App\Entity\Service;
use App\Entity\StoreService;
use App\Repository\TypeServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ServiceSetting
{
    private $security;

    private $typeServiceRepository;

    private $manager;

    public function __construct(Security $security, TypeServiceRepository $typeServiceRepository, EntityManagerInterface $manager)
    {
        $this->security = $security;
        $this->typeServiceRepository = $typeServiceRepository;
        $this->manager = $manager;
    }

    public function setType(Service $service): Service
    {
        if($this->security->isGranted('ROLE_ADMIN_STORE') ){
            $type = $this->typeServiceRepository->findOneBy(['name' => 'store']);
            $service->setType($type);
        }elseif ($this->security->isGranted('ROLE_ADMIN_COMPANY')){
            $type = $this->typeServiceRepository->findOneBy(['name' => 'company']);
            $service->setType($type);
        }

        return $service;
    }

    public function setToParent(Service $service): Service
    {
        if($this->security->isGranted('ROLE_ADMIN_STORE') ){
            //avoid manyToMany with StoreService entity
            $storeService = $this->setStoreService($this->security->getUser()->getStore(), $service);

            $this->security->getUser()->getStore()->addService($storeService);
        }elseif ($this->security->isGranted('ROLE_ADMIN_COMPANY')){
            $this->security->getUser()->getCompany()->addService($service);
        }

        return $service;
    }

    public function setStoreService($store, $service)
    {
        $storeService = new StoreService();
        $storeService->setStore($store);
        $storeService->setService($service);

        $this->manager->persist($storeService);

        return $storeService;
    }
}