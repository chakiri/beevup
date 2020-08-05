<?php

namespace App\Service;
use App\Repository\CompanyRepository;
use App\Repository\StoreRepository;



class Communities
{
    private $storeRepository;

    public function __construct(StoreRepository $storeRepository)
    {

        $this->storeRepository  = $storeRepository;


    }

    public function getStoresAround(int $km){

        $stores = [];

        return $stores;

    }

    public function getAllCompanies(Company $store)
    {
        $companies =[];
        $localCompanies = $this->storeRepository ->findOneBy(['id' => $store->getId()])->getCompanies();
        $externalCompanies = $this->storeRepository ->findOneBy(['id' => $store->getId()])->getExternalCompanies();
        array_push($companies, $localCompanies, $externalCompanies);
        return $companies;
    }

    public function calculateDistanceBetweenCompanies($currentCompany, $company)
    {
        // we can use open street map to calculate the distance
    }


    // this the function to calculate distance between two longitude and latitude
    // we need only to replace $lat1, $lon1 by $currentCompany->getLatitude() and $currentCompany->getLongitude(), the same for $company
    function distance($lat1, $lon1, $lat2, $lon2, $unit) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }
        else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
    }


}


