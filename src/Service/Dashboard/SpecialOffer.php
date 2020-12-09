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

    /**
     * Find the last special offer between services and associated services
     *
     * @param $allCompanies
     * @param $store
     * @return int|mixed|string|null
     */
    public function find($allCompanies, $store)
    {
        $lastSpecialOfferService = $this->serviceRepository->findOneByIsDiscovery($allCompanies, $store);
        $lastStoreServiceDiscovery = $this->storeServicesRepository->findOneByIsDiscovery($store);

        if ($lastStoreServiceDiscovery){
            $lastSpecialOfferPf = $lastStoreServiceDiscovery->getService();

            if ($lastSpecialOfferService->getCreatedAt() > $lastStoreServiceDiscovery->getCreatedAt()) $lastSpecialOffer = $lastSpecialOfferService;
            else $lastSpecialOffer = $lastSpecialOfferPf;

            return $lastSpecialOffer;
        }

        return $lastSpecialOfferService;
    }
}