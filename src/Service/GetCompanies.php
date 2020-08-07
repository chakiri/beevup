<?php

namespace App\Service;


use App\Entity\Store;
use App\Repository\CompanyRepository;

class GetCompanies
{
    private $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function getLocalCompanies(Store $store): array
    {
        $companiesIds =[];
        $companies = $store->getCompanies();
        foreach ($companies as $company)
        {

            array_push($companiesIds, $company->getId());
        }

        return $companiesIds;

    }

    public function getExternalCompanies(Store $store): array
    {
        $companiesIds = $store->getExternalCompanies();

        $companies = [];
        if($companiesIds) {
            foreach ($companiesIds as $id) {
               // $company = $this->companyRepository->findOneById($id);
                array_push($companies, $id);
            }
        }

        return $companies;
    }

    public function getAllCompanies(Store $store)
    {
        $localCompanies = $this->getLocalCompanies($store);
        $externalCompanies = $this->getExternalCompanies($store);

        $companies = array_merge($localCompanies, $externalCompanies);
        return $companies;
    }
}