<?php

namespace App\Service\Search;

use App\Repository\CompanyRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;

class SearchHandler
{
    private CompanyRepository $companyRepository;

    private ServiceRepository $serviceRepository;

    private Security $security;

    private UserRepository $userRepository;

    public function __construct(CompanyRepository $companyRepository, ServiceRepository $serviceRepository, UserRepository $userRepository, Security $security)
    {
        $this->companyRepository = $companyRepository;
        $this->serviceRepository = $serviceRepository;
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    /**
     * Get results internal search
     * Results will contain companies and services
     */
    public function getResults($allCompanies, $name, $isService = false, $isCompany = false, $category = null, $isDiscovery = false): array
    {
        $companies = [];
        $services = [];

        //If filter is empty or if isCompany choose
        if ($isCompany === true || ($isCompany === false && $isService === false)){
            $companies = $this->getAllCompanies($allCompanies, $name);
        }

        if ($isService === true || ($isCompany === false && $isService === false)){
            $services = $this->getAllServices($allCompanies, $name, $category, $isDiscovery);
        }

        //Merge result
        $items = array_merge($companies, $services);

        //Sort by updatedAt
        usort($items, [$this, 'orderByDate']);

        return [
            'items' => $items,
            'companies' => $companies,
            'services' => $services
        ];
    }

    /**
     * Get results external search
     * Results will contain all companies of all results
     */
    public function getResultsExtern($allCompanies, $name): array
    {
        $companies = $this->getAllCompanies($allCompanies, $name);

        $services = $this->getAllServices($allCompanies, $name);

        //Get companies of services
        $companiesServices = $this->getCompaniesOfServices($services);

        //Merge and remove duplication
        $allCompanies = array_unique(array_merge($companies, $companiesServices));

        //Sort by updatedAt
        usort($allCompanies, [$this, 'orderByDate']);

        return $allCompanies;
    }

    /**
     * Get companies from search and companies of users result
     */
    public function getAllCompanies($allCompanies, $name): array
    {
        //Get companies
        $companies = $this->companyRepository->findBySearch($name, $allCompanies);

        //Get users
        $users = $this->userRepository->findByValue($name, $allCompanies);

        //Get companies of all users like query
        foreach ($users as $user){
            //Push if company not existing in array
            if (!in_array($user->getCompany(), $companies))
                array_push($companies, $user->getCompany());
        }

        return $companies;
    }

    /**
     * Get Service of community and associated store services
     */
    private function getAllServices($allCompanies, $name, $category = null, $isDiscovery = null): array
    {
        $services = $this->serviceRepository->findSearch($name, $category, $isDiscovery, $allCompanies);

        //Add related generic services of store if match query
        if ($this->security->getUser()){
            $storeServices = $this->security->getUser()->getStore()->getServices();
            $storeServices = $this->serviceRepository->findSearchStoreServices($storeServices, $name, $category, $isDiscovery);
            $services = array_merge($services, $storeServices);
        }

        return $services;
    }

    /**
     * Get all companies of services array
     */
    private function getCompaniesOfServices(array $services): array
    {
        $companies = [];
        foreach ($services as $service){
            $company = $service->getUser()->getCompany();

            //Avoid store services
            if ($company) $companies[] = $company;
        }

        return $companies;
    }

    /**
     * Function used in usort on top
     */
    public function orderByDate($a, $b)
    {
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