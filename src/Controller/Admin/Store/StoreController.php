<?php
namespace App\Controller\Admin\Store;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use App\Service\Map ;


class StoreController extends EasyAdminController
{
    private $map;

    public function __construct()
    {
       $this->map = new Map();
    }
    
    public function persistStoreEntity($store)
    {
        $adresse = $store->getAddressNumber().' '.$store->getAddressStreet().' '.$store->getAddressPostCode().' '.$store->getCity().' '.$store->getCountry();
        $coordonnees =  $this->map->geocode($adresse);

        $store->setLatitude($coordonnees[0]);
        $store->setLongitude($coordonnees[1]);
        parent::persistEntity($store);
   }

    public function updateStoreEntity($store)
    {
        $adresse = $store->getAddressNumber().' '.$store->getAddressStreet().' '.$store->getAddressPostCode().' '.$store->getCity().' '.$store->getCountry();
        $coordonnees =  $this->map->geocode($adresse);
       if($coordonnees !=null) {
           $store->setLatitude($coordonnees[0]);
           $store->setLongitude($coordonnees[1]);
       }
       // we need to add a flush message to alert user that the address is not correct

        parent::updateEntity($store);
        
    }

    protected function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null)
    {
        $store = $this->getUser()->getStore();
        if($this->getUser()->getType()->getId() !=5 )
        {
            $dqlFilter = sprintf('entity.id = %s', $store->getId());
        }

        $list = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);
        return $list;
    }
}