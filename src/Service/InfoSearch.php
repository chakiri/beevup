<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Store;
use App\Entity\User;
use App\Repository\RecommandationRepository;
use Symfony\Component\Security\Core\Security;

class InfoSearch
{
    private $recommandationRepository;
    private $communities;
    private $currentUser;

    public function __construct(RecommandationRepository $recommandationRepository, Communities $communities, Security $security)
    {
        $this->recommandationRepository = $recommandationRepository;
        $this->communities = $communities;
        $this->currentUser = $security->getUser();
    }

    public function getNbRecommandations($item, $nbRecommandations): array
    {
        if ($item instanceof User){
            $company = $item->getCompany();
            //Get nb recommandations of each company item
            $nbRecommandation = count($this->recommandationRepository->findByUserRecommandation($item, 'Validated'));
        }
        elseif ($item instanceof Company){
            $company = $item;
            //Get nb recommandations of each company item
            $nbRecommandation = count($this->recommandationRepository->findByCompanyServices($company, 'Validated')) + count($this->recommandationRepository->findByCompanyWithoutServices($company, 'Validated'));
        }
        $nbRecommandations[$company->getId()] = $nbRecommandation;

        return $nbRecommandations;
    }

    public function getDistance($item, $distances, $store = null): array
    {
        if ($item instanceof User)  $company = $item->getCompany();
        elseif ($item instanceof Company)   $company = $item;

        if ($this->currentUser)
            $distance = $this->communities->calculateDistanceBetween($company, $this->currentUser->getCompany(), 'K');
        elseif (!$this->currentUser && $store)
            $distance = $this->communities->calculateDistanceBetween($company, $store, 'K');
        else
            $distance = null;

        $distances[$company->getId()] = $distance;

        return $distances;
    }

    public function getInfosCompanies(array $companies, Store $store = null): array
    {
        $infos = [];
        $nbRecommandations = [];
        $distances = [];
        foreach ($companies as $company){
            $nbRecommandations = $this->getNbRecommandations($company, $nbRecommandations);
            //Get nb Km between current sottore company and company item
            $distances = $this->getDistance($company, $distances, $store);
        }
        $infos['nbRecommandations'] = $nbRecommandations;
        $infos['distances'] = $distances;

        return $infos;
    }
}