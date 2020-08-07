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
        $companies = $store->getCompanies()->toArray();
        return $companies;
    }

    public function getExternalCompanies(Store $store): array
    {
        $companiesIds = $store->getExternalCompanies();

        $companies = [];
        if($companiesIds) {
            foreach ($companiesIds as $id) {
                $company = $this->companyRepository->findOneById($id);
                array_push($companies, $company);
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