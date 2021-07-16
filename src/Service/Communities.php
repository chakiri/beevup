<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Store;
use App\Repository\StoreRepository;

class Communities
{
    private $storeRepository;

    public function __construct(StoreRepository $storeRepository)
    {
        $this->storeRepository  = $storeRepository;
    }

    public function getStoresAround(Company $currentCompany,int $km): array
    {
        $stores = [];
        $allStores = $this->storeRepository->findAll();
        foreach ($allStores as $store)
        {
            if($this->calculateDistanceBetween($currentCompany, $store, 'K') <= $km)
            {
                array_push($stores, $store);
            }
        }

        return $stores;
    }


    public function calculateDistanceBetween($currentCompany, $element, $unit)
    {
        if ($element instanceof Store || $element instanceof Company){
            $lat1 = $currentCompany->getLatitude();
            $lon1 = $currentCompany->getLongitude();

            $lat2 = $element->getLatitude();
            $lon2 = $element->getLongitude();

            return $this->calculateDistanceLonLat($lat1, $lon1, $lat2, $lon2, $unit);
        }

        return 0;
    }

    public function calculateDistanceLonLat($lat1, $lon1, $lat2, $lon2, $unit)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }else {
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

    /**
     * Get closer store form geolocalisation
     */
    public function getCloserStore($stores, $lat, $lon): Store
    {
        //Declare variable
        $closerStore = null;
        $smallerDistance = null;

        foreach($stores as $store) {
            //Get distance of each store
            $distance = $this->calculateDistanceLonLat($lat, $lon, $store->getLatitude(), $store->getLongitude(), 'K');

            //If smallerDistance is null get first distance/store
            $smallerDistance = $smallerDistance ?? $distance;
            $closerStore = $closerStore ?? $store;

            //Compare distances and get the smaller
            if ($distance < $smallerDistance) {
                $smallerDistance = $distance;
                $closerStore = $store;
            }
        }

        return $closerStore;
    }

}


