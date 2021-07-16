<?php

namespace App\Service\Search;

use App\Entity\Company;
use App\Entity\Service;
use App\Repository\CompanyRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use App\Service\Communities;
use Symfony\Component\Security\Core\Security;

class SearchHandler
{
    private CompanyRepository $companyRepository;

    private ServiceRepository $serviceRepository;

    private Security $security;

    private UserRepository $userRepository;
    private Communities $communities;

    public function __construct(CompanyRepository $companyRepository, ServiceRepository $serviceRepository, UserRepository $userRepository, Security $security, Communities $communities)
    {
        $this->companyRepository = $companyRepository;
        $this->serviceRepository = $serviceRepository;
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->communities = $communities;
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

        //Sort by is labeled and createdAt
        usort($items, [$this, 'orderByIsLabeledAndCreatedAt']);

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
    public function getResultsExtern($query, $latitudeSearch = null, $longitudeSearch = null): array
    {
        $allCompanies = $this->companyRepository->findAll();

        $services = $this->getAllServices($allCompanies, $query);

        if ($latitudeSearch && $longitudeSearch){
            //Sort by is labeled and createdAt
            usort($services, function($a, $b) use ($latitudeSearch, $longitudeSearch) {
                //Get companies of services
                $companyServiceA = $this->getCompany($a);
                $companyServiceB = $this->getCompany($b);

                $distanceA = $this->communities->calculateDistanceLonLat($companyServiceA->getLatitude(), $companyServiceA->getLongitude(), $latitudeSearch, $longitudeSearch, 'K');
                $distanceB = $this->communities->calculateDistanceLonLat($companyServiceB->getLatitude(), $companyServiceB->getLongitude(), $latitudeSearch, $longitudeSearch, 'K');

                if ($distanceA === $distanceB) {
                    return 0;
                }
                return ($distanceA < $distanceB) ? -1 : 1;
            });
        }

        return $services;
    }

    public function getCompany($service)
    {
        $company = $service->getUser()->getCompany();
        if (!$company){
            $company = $service->getUser()->getStore();
        }

        return $company;
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
     * Function to sort by createdAt
     */
    public function orderByDate($a, $b): int
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

    /**
     * Function to sort by company labeled
     */
    public function orderByIsLabeled($a, $b): int
    {
        // true - true == 0, true - false == 1, false - true == -1
        return $this->companyItemIsLabeled($b) - $this->companyItemIsLabeled($a);
    }

    /**
     * Function to sort by company labeled and createdAt
     */
    public function orderByIsLabeledAndCreatedAt($a, $b): int
    {
        if ($this->companyItemIsLabeled($b) - $this->companyItemIsLabeled($a) === 0){
            return ($a->getCreatedAt() > $b->getCreatedAt()) ? -1 : 1;
        }else{
            return $this->companyItemIsLabeled($b) - $this->companyItemIsLabeled($a);
        }
    }


    /**
     * Function to return company is labeled depending on item
     */
    private function companyItemIsLabeled($element): bool
    {
        //Get company from item
        if ($element instanceof Service){
            $company = $element->getUser()->getCompany();
        }elseif ($element instanceof Company){
            $company = $element;
        }

        //Check if is labeled
        return $company && $company->getLabel() && $company->getLabel()->isLabeled() == true;
    }


}