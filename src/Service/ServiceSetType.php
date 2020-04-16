<?php

namespace App\Service;


use App\Entity\Service;
use App\Repository\TypeServiceRepository;
use Symfony\Component\Security\Core\Security;

class ServiceSetType
{
    private $security;

    private $typeServiceRepository;

    public function __construct(Security $security, TypeServiceRepository $typeServiceRepository)
    {
        $this->security = $security;
        $this->typeServiceRepository = $typeServiceRepository;
    }

    public function set(Service $service): Service
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
}