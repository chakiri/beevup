<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Repository\SubscriptionRepository;
use App\Service\Communities;
use App\Service\Factory\SubscriptionFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    /**
     * @Route("/subscription", name="subscription")
     */
    public function index()
    {
        return $this->render('subscription/index.html.twig');
    }

    /**
     * @Route("/subscription/premium/{id}", name="subscription_premium")
     */
    public function premium(Offer $offer, EntityManagerInterface $manager, Communities $communities, SubscriptionRepository $subscriptionRepository)
    {
        $company = $this->getUser()->getCompany();

        $subscription = $subscriptionRepository->findOneBy(['company' => $company, 'offer' => $offer]);
        if (!$subscription){
            //New subscription
            $subscription = SubscriptionFactory::create($company, $offer);
        }else{
            //Update subscription
            $subscription = SubscriptionFactory::update($subscription);
        }

        $manager->persist($subscription);

        //Get eligibles Stores
        $stores = $communities->getStoresAround($offer->getKm());

        //Put in each Store, current Company in ExternalCompanies attribute
        foreach ($stores as $store){
            $store->addExternalCompany($company->getId());
            $manager->persist($store);
        }

        $manager->flush();

        return $this->render('subscription/index.html.twig');
    }
}
