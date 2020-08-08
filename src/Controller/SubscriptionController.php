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

        //Tomporary//
        $nbMonths = 1;

        $subscription = $subscriptionRepository->findOneBy(['company' => $company]);
        if (!$subscription){
            //New subscription
            $subscription = SubscriptionFactory::create($company, $offer, $nbMonths);
        }else{
            //Update subscription
            $subscription = SubscriptionFactory::update($subscription, $offer, $nbMonths);
        }

        $manager->persist($subscription);

        //Get eligibles Stores
        $stores = $communities->getStoresAround($company, $offer->getKm());

        //Put in each Store, current Company in ExternalCompanies attribute
        foreach ($stores as $store){
            $store->addExternalCompany($company->getId());
            $manager->persist($store);
        }

        $manager->flush();

        $this->addFlash('success', 'Votre abonnement a bien été pris en compte !');

        return $this->redirectToRoute('dashboard');
    }
}
