<?php

namespace App\Service;


use App\Entity\Company;
use App\Repository\CompanyRepository;

class GetCompanies
{
    private $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function getLocalCompanies(Company $company): array
    {
        $companies = $company->getStore()->getCompanies();

        return $companies;
    }

    public function getExternalCompanies(Company $company): array
    {
        $companiesIds = $company->getStore()->getExternalCompanies();

        $companies = [];
        foreach ($companiesIds as $id){
            $company = $this->companyRepository->findOneById($id);
            array_push($companies, $company);
        }

        return $companies;
    }

    public function getAllCompanies(Company $company)
    {
        $localCompanies = $this->getLocalCompanies($company);
        $externalCompanies = $this->getExternalCompanies($company);

        $companies = array_merge($localCompanies, $externalCompanies);
        return $companies;
    }
}