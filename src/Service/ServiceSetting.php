<?php

namespace App\Service;


use App\Entity\Service;
use App\Repository\TypeServiceRepository;
use Symfony\Component\Security\Core\Security;

class ServiceSetting
{
    private $security;

    private $typeServiceRepository;

    public function __construct(Security $security, TypeServiceRepository $typeServiceRepository)
    {
        $this->security = $security;
        $this->typeServiceRepository = $typeServiceRepository;
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
            $this->security->getUser()->getStore()->addService($service);
        }elseif ($this->security->isGranted('ROLE_ADMIN_COMPANY')){
            $this->security->getUser()->getCompany()->addService($service);
        }

        return $service;
    }
}