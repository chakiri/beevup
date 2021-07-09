<?php

namespace App\Service\Search;

use App\Entity\Company;
use App\Entity\Service;
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
    public function getResultsExtern($allCompanies, $name): array
    {
        $companies = $this->getAllCompanies($allCompanies, $name);

        $services = $this->getAllServices($allCompanies, $name);

        //Get companies of services
        $companiesServices = $this->getCompaniesOfServices($services);

        //Merge and remove duplication
        $allCompanies = array_unique(array_merge($companies, $companiesServices));

        //Sort by is labeled and createdAt
        usort($allCompanies, [$this, 'orderByIsLabeledAndCreatedAt']);

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

    /**
     * Function to get all services by postal code
     */
    public function getAllServicesByPostalCode($query, $postalCode)
    {
        //get all companies of platform
        $allCompanies = $this->companyRepository->findAll();
        $services = $this->getAllServices($allCompanies, $query);


        //classer ces services par eloignements du code postal
            //recup lat lon de postal code
            //recup lat lon de entreprise du service
            //verifier la distance
            //classer tous les services dans un tableau par distance minim
    }

}