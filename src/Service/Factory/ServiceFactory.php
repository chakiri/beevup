<?php

namespace App\Service\Factory;


use App\Entity\Service;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;


class ServiceFactory
{
    public static function create(Service $service, User $user, $type): Service
    {
        $newService = new Service();

        $newService
            ->setUser($user)
            ->setType($type)
            ->setTitle($service->getTitle())
            ->setCategory($service->getCategory())
            ->setDescription($service->getDescription())
            ->setPrice($service->getPrice())
            ->setCreatedAt(new \DateTime())
            ->setIntroduction($service->getIntroduction())
            ->setIsQuote($service->getIsQuote())
            ->setIsDiscovery($service->getIsQuote())
            ->setToIndividuals($service->getToIndividuals())
            ->setIsDiscovery(false)
            ->setToProfessionals($service->getToProfessionals())
            ->setFilename($service->getFilename())
        ;

        return $newService;
    }
}