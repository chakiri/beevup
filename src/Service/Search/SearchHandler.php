<?php


namespace App\Service\Search;


use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;

class SearchHandler
{
    private $companyRepository;

    private ServiceRepository $serviceRepository;

    private Security $security;

    public function __construct(CompanyRepository $companyRepository, ServiceRepository $serviceRepository, Security $security)
    {
        $this->companyRepository = $companyRepository;
        $this->serviceRepository = $serviceRepository;
        $this->security = $security;
    }

    /**
     * Get results from companies and users
     */
    public function getResults($allCompanies, $name, $service, $company, $isExclusif): array
    {
        //Get companies
        if ()
        $companies = $this->companyRepository->findBySearch($name, $allCompanies);

        //Get users
        //$users = $this->userRepository->findByValue($query, $allCompanies);

        //Get service
        $services = $this->getAllServices($query, $allCompanies);

        //Merge results
        $items = array_merge($companies, $services);

        //Sort by updatedAt
        usort($items, [$this, 'orderByDate']);

        return [
            'items' => $items,
            'companies' => $companies,
            'services' => $services
        ];
    }

    //Get Service of community and associated store services
    private function getAllServices($query, $allCompanies)
    {
        $services = $this->serviceRepository->findByLocalServices($allCompanies);

        //Add related generic services of store
        $storeServices = $this->security->getUser()->getStore()->getServices();
        foreach ($storeServices as $storeService) {
            array_push($services, $storeService->getService());
        }

        return $services;
    }

    public function orderByDate($a, $b){
        //return 0 if equal
        if ($a->getCreatedAt() === $b->getCreatedAt()) {
            return 0;
        }elseif ($a->getCreatedAt() > $b->getCreatedAt()) {
            return -1;
        }else {
            return 1;
        }
    }

}