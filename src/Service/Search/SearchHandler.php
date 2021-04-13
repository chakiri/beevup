<?php

namespace App\Service\Search;

use App\Entity\Company;
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
     * Get results from companies and users
     */
    public function getResults($allCompanies, $name, $isService = false, $isCompany = false, $category = null, $isDiscovery = false): array
    {
        $companies = [];
        $services = [];

        //If filter is empty or if isCompany choose
        if ($isCompany === true || ($isCompany === false && $isService === false)){
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
        }

        if ($isService === true || ($isCompany === false && $isService === false)){
            //Get service
            $services = $this->getAllServices($name, $category, $isDiscovery, $allCompanies);
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

    //Get Service of community and associated store services
    private function getAllServices($name, $category, $isDiscovery, $allCompanies)
    {
        $services = $this->serviceRepository->findSearch($name, $category, $isDiscovery, $allCompanies);

        //Add related generic services of store if match query
        $storeServices = $this->security->getUser()->getStore()->getServices();
        $storeServices = $this->serviceRepository->findSearchStoreServices($storeServices, $name, $category, $isDiscovery);

        return array_merge($services, $storeServices);
    }

    //Function used in usort on top
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