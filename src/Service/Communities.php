<?php

namespace App\Service;
use App\Repository\StoreRepository;


class Communities
{
    private $storeRepository;

    public function __construct(StoreRepository $storeRepository)
    {
        $this->storeRepository  = $storeRepository;
    }

    public function getStoresAround($currentCompany,int $km)
    {
        $stores = [];
        $allStores = $this->storeRepository->findAll();
        foreach ($allStores as $store)
        {
            if($this->calculateDistanceBetweenCompanyAndStores($currentCompany, $store, 'K') <= $km)
            {
                array_push($stores, $store);
            }
        }

        return $stores;
    }


    public function calculateDistanceBetweenCompanyAndStores($currentCompany, $store, $unit)
    {
        $lat1 = $currentCompany->getLatitude();
        $lon1 = $currentCompany->getLongitude();

        $lat2 = $store->getLatitude();
        $lon2 = $store->getLongitude();

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


