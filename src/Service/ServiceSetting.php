<?php

namespace App\Service;


use App\Entity\Service;
use App\Entity\StoreService;
use App\Repository\RecommandationRepository;
use App\Repository\StoreServicesRepository;
use App\Repository\TypeServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ServiceSetting
{
    private $security;

    private $typeServiceRepository;

    private $manager;

    private$recommandationRepository;

    private $storeServicesRepository;

    private $communities;

    public function __construct(Security $security, TypeServiceRepository $typeServiceRepository, EntityManagerInterface $manager, RecommandationRepository $recommandationRepository, StoreServicesRepository $storeServicesRepository, Communities $communities)
    {
        $this->security = $security;
        $this->typeServiceRepository = $typeServiceRepository;
        $this->manager = $manager;
        $this->recommandationRepository = $recommandationRepository;
        $this->storeServicesRepository = $storeServicesRepository;
        $this->communities = $communities;
    }

    public function setType(Service $service): Service
    {
        if($this->security->isGranted('ROLE_ADMIN_STORE') ){
            $type = $this->typeServiceRepository->findOneBy(['name' => 'store']);
            $service->setType($type);
        }elseif ($this->security->isGranted('ROLE_ADMIN_COMPANY')){
            $type = $this->typeServiceRepository->findOneBy(['name' => 'company']);
            $service->setType($type);
        }elseif ($this->security->isGranted('ROLE_USER')){
            if ($this->security->getUser()->getType()->getId() == 6){
                $type = $this->typeServiceRepository->findOneBy(['name' => 'company']);
                $service->setType($type);
            }elseif ($this->security->getUser()->getType()->getId() == 2){
                $type = $this->typeServiceRepository->findOneBy(['name' => 'store']);
                $service->setType($type);
            }
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

    public function getNbRecommandations($service, $nbRecommandations): array
    {
        if ($service->getType()){
            if ($service->getType()->getName() == 'company') {
                $nbRecommandation = count($this->recommandationRepository->findBy(['company' => $company = $service->getUser()->getCompany(), 'service' => $service, 'status'=>'Validated']));
                $nbRecommandations[$service->getId()] = $nbRecommandation;
            }elseif ($service->getType()->getName() == 'store'){
                $nbRecommandation = count($this->recommandationRepository->findBy(['store' => $store = $service->getUser()->getStore(), 'service' => $service, 'status'=>'Validated']));
                $nbRecommandations[$service->getId()] = $nbRecommandation;
            }elseif ($service->getType()->getName() == 'plateform'){
                //Get assocaition if exist
                $storeService = $this->storeServicesRepository->findOneBy(['service' => $service, 'store' => $this->security->getUser()->getStore()]);
                if ($storeService){
                    $nbRecommandation = count($this->recommandationRepository->findBy(['company' => null, 'service' => $service, 'status'=>'Validated']));
                    $nbRecommandations[$service->getId()] = $nbRecommandation;
                }
            }
        }

        return $nbRecommandations;
    }

    public function getDistance($service, $distances): array
    {
        if ($service->getType()->getName() == 'company') $item = $service->getUser()->getCompany();
        elseif ($service->getType()->getName() == 'store') $item = $service->getUser()->getStore();
        elseif ($service->getType()->getName() == 'plateform'){
            //Get assocaition if exist
            $storeService = $this->storeServicesRepository->findOneBy(['service' => $service, 'store' => $this->security->getUser()->getStore()]);
            if ($storeService)  $item = $this->security->getUser()->getStore();
        }

        if ($this->security->getUser()->getCompany()){
            $distance = $this->communities->calculateDistanceBetween($this->security->getUser()->getCompany(), $item ?? null, 'K');
            $distances[$service->getId()] = $distance;
        }

        return $distances;
    }
}