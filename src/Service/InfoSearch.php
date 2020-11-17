<?php


namespace App\Service;


use App\Entity\Company;
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

    public function getDistance($item, $distances): array
    {
        if ($item instanceof User)  $company = $item->getCompany();
        elseif ($item instanceof Company)   $company = $item;

        $distance = $this->communities->calculateDistanceBetween($company, $this->currentUser->getCompany(), 'K');
        $distances[$company->getId()] = $distance;

        return $distances;
    }
}