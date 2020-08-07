<?php

namespace App\Service\Factory;

use App\Entity\Company;
use App\Entity\Offer;
use App\Entity\Subscription;

class SubscriptionFactory
{

    public static function create(Company $company, Offer $offer, int $nbMonths): Subscription
    {
        $currentDate = new \Datetime();
        //Interval one month from now
        $interval = new \DateInterval('P' . $nbMonths . 'M');

        $subscription = new Subscription();
        $subscription
            ->setCompany($company)
            ->setOffer($offer)
            ->setStartDate($currentDate)
            ->setEndDate($currentDate->add($interval))
            ->setIsExpired(false)
            ->setNbMonths($nbMonths)
            ;

        return $subscription;
    }

    public static function update(Subscription $subscription, int $nbMonths): Subscription
    {
        $currentDate = new \Datetime();
        //Interval one month from now
        $interval = new \DateInterval('P' . $nbMonths . 'M');

        $subscription
            ->setStartDate($currentDate)
            ->setEndDate($currentDate->add($interval))
            ->setIsExpired(false)
            ->setNbMonths($nbMonths)
        ;

        return $subscription;
    }
}