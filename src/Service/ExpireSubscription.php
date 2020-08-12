<?php

namespace App\Service;


use App\Entity\Company;
use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;

class ExpireSubscription
{

    private $manager;

    private $communities;

    public function __construct(EntityManagerInterface $manager, Communities $communities)
    {
        $this->manager = $manager;
        $this->communities = $communities;
    }

    public function expired(Subscription $subscription): bool
    {
        $now = new \Datetime();
        if ($now > $subscription->getEndDate()){
            return true;
        }
        return false;
    }

    public function set(Subscription $subscription): void
    {
        $subscription->setIsExpired(true);

        //Unset company from all stores offer
        $this->unsetCompanyFromAllStores($subscription->getCompany());

        $this->manager->persist($subscription);

        $this->manager->flush();
    }

    public function unsetCompanyFromAllStores(Company $company): void
    {
        $stores = $this->communities->getStoresAround($company, $company->getSubscription()->getOffer()->getKm());

        foreach ($stores as $store){
            $store->removeExternalCompany($company->getId());
            $this->manager->persist($store);
        }
    }
}