<?php


namespace App\Service\Search;


use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;

class SearchHandler
{
    private $companyRepository;

    private $userRepository;

    public function __construct(CompanyRepository $companyRepository, UserRepository $userRepository)
    {
        $this->companyRepository = $companyRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Get results from companies and users
     */
    public function getResults($allCompanies, $query): array
    {
        //Get companies
        $companies = $this->companyRepository->findBySearch($query, $allCompanies);

        //Get users
        $users = $this->userRepository->findByValue($query, $allCompanies);

        $results = array_merge($companies, $users);

        //Sort by updatedAt
        usort($results, [$this, 'orderByDate']);

        return $results;
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