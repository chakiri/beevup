<?php

namespace App\Service\Company;

use App\Repository\CompanyRepository;
use App\Repository\ProfilRepository;
use App\Repository\ServiceRepository;

class CompanySearch
{
    private $serviceRepository;

    private $profilRepository;

    private $companyRepository;

    public function __construct(ServiceRepository $serviceRepository, ProfilRepository $profilRepository, CompanyRepository $companyRepository)
    {

        $this->serviceRepository = $serviceRepository;
        $this->profilRepository = $profilRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * Get companies from search
     */
    public function getCompanies($allCompanies, $query): array
    {
        //Declare array
        $results = [];

        //Get companies from services search
        $services =  $this->serviceRepository->findByQuery($allCompanies, $query);
        foreach ($services as $service){
            if (!in_array($service->getUser()->getCompany(), $results, true)) {
                $results[] = $service->getUser()->getCompany();
            }
        }

        //Get companies from profiles search
        $profiles =  $this->profilRepository->findByQuery($allCompanies, $query);
        foreach ($profiles as $profile){
            if ($profile->getUser() && !in_array($profile->getUser()->getCompany(), $results, true)){
                $results[] = $profile->getUser()->getCompany();
            }
        }

        //Get companies from profiles search
        $companies =  $this->companyRepository->findBySearch($query, $allCompanies);
        foreach ($companies as $company){
            if (!in_array($company, $results, true)) {
                $results[] = $company;
            }
        }

        return $results;
    }

}