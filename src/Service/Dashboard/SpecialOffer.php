<?php


namespace App\Service\Dashboard;


use App\Repository\ServiceRepository;
use App\Repository\StoreServicesRepository;

class SpecialOffer
{
    private $serviceRepository;

    private $storeServicesRepository;

    public function __construct(ServiceRepository  $serviceRepository, StoreServicesRepository  $storeServicesRepository)
    {
        $this->serviceRepository = $serviceRepository;
        $this->storeServicesRepository = $storeServicesRepository;
    }

    public function find($allCompanies, $store)
    {
        $lastSpecialOfferService = $this->serviceRepository->findOneByIsDiscovery($allCompanies, $store);

        $lastSpecialOfferStoreService = $this->storeServicesRepository->findOneByIsDiscovery($store)->getService();

        dd($lastSpecialOfferService, $lastSpecialOfferStoreService);
        if ($lastSpecialOfferService->getCreatedAt() < $lastSpecialOfferStoreService->getCreatedAt())
            $lastSpecialOffer = $lastSpecialOfferService;
        else $lastSpecialOffer = $lastSpecialOfferStoreService;

        dd($lastSpecialOffer);
        return $lastSpecialOffer;
    }
}