<?php

namespace App\Controller;

use App\Entity\Search;
use App\Entity\Store;
use App\Entity\User;
use App\Form\SearchStoreType;
use App\Form\SearchType;
use App\Repository\CompanyRepository;
use App\Repository\FavoritRepository;
use App\Repository\ProfilRepository;
use App\Repository\ServiceRepository;
use App\Repository\StoreRepository;
use App\Repository\UserRepository;
use App\Service\InfoSearch;
use App\Service\ServiceSetting;
use App\Service\Session\ExternalStoreSession;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\GetCompanies;


class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index(Request $request, GetCompanies $getCompanies, UserRepository $userRepository, FavoritRepository $favoritRepository, CompanyRepository $companyRepository, InfoSearch $infoSearch)
    {
        $allCompanies = $getCompanies->getAllCompanies( $this->getUser()->getStore());

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        //$items = $userRepository->findByIsCompletedProfile($allCompanies);
        $items = $companyRepository->findBySearch('', $allCompanies);

        $favoris = $favoritRepository->findBy(['user'=> $this->getUser()]);
        $favorisUsersIds = [];
        $favorisCompaniesIds = [];
        foreach ($favoris as $favorit)
        {
            array_push($favorisUsersIds, $favorit->getFavoritUser()->getId());
            if($favorit->getCompany()!= null) {
                array_push($favorisCompaniesIds, $favorit->getCompany()->getId());
            }
        }

        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
            if ($search->getType() == 'company'){
                $items = $companyRepository->findBySearch($search->getName(), $allCompanies);
            }elseif ($search->getType() == 'users'){
                $items = $userRepository->findByValue($search->getName(), $allCompanies);
            }
        }

        //Get infos of companies
        $infos = $infoSearch->getInfosCompanies($items);

        return $this->render('search/search.html.twig', [
            'SearchForm' => $form->createView(),
            'items' => $items,
            'favoris' => $favoris,
            'favorisUsersIds' => $favorisUsersIds,
            'favorisCompaniesIds' => $favorisCompaniesIds,
            'nbRecommandations' => $infos['nbRecommandations'],
            'distances' => $infos['distances'],
            'isUser' => $items ? $items[0] instanceof User : false,
        ]);

    }

    /**
     * @Route("/external/search", name="external_search")
     */
    public function externalSearch(Request $request, StoreRepository $storeRepository, ServiceRepository $serviceRepository, ProfilRepository $profilRepository, CompanyRepository $companyRepository, GetCompanies $getCompanies, ServiceSetting $serviceSetting, InfoSearch $infoSearch, ExternalStoreSession $externalStoreSession)
    {
        if ($request->get('store'))  $store = $storeRepository->findOneBy(['reference' => $request->get('store')]);
        else    $store = $storeRepository->findOneBy(['reference' => 'BV001']);

        if (!$store) return $this->render('bundles/TwigBundle/Exception/error404.html.twig');

        //Set reference in session
        $externalStoreSession->setReference($store);

        //Get local services of store
        $allCompanies = $getCompanies->getAllCompanies($store);
        $services = $serviceRepository->findByLocalServicesWithLimit($allCompanies, 12);
        $companies = $companyRepository->findBySearch('', $allCompanies);

        $form = $this->createForm(SearchStoreType::class, null, ['store' => $store]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $results = [];

            //Get companies from services search
            $services =  $serviceRepository->findByQuery($allCompanies, $form->get('querySearch')->getData());
            foreach ($services as $service){
                if (!in_array($service->getUser()->getCompany(), $results))
                    array_push($results, $service->getUser()->getCompany());
            }

            //Get companies from profiles search
            $profiles =  $profilRepository->findByQuery($allCompanies, $form->get('querySearch')->getData());
            foreach ($profiles as $profile){
                if (!in_array($profile->getUser()->getCompany(), $results))
                    array_push($results, $profile->getUser()->getCompany());
            }

            //Get companies from profiles search
            $companies =  $companyRepository->findBySearch($form->get('querySearch')->getData(), $allCompanies);
            foreach ($companies as $company){
                if (!in_array($company, $results))
                    array_push($results, $company);
            }

            //Get infos from each company
            $infos = $infoSearch->getInfosCompanies($companies, $store);

            return $this->render("search/external/searchStoreResult.html.twig", [
                'query' => $form->get('querySearch')->getData(),
                'results' => $results,
                'nbRecommandationsCompanies' => $infos['nbRecommandations'],
                'distancesCompanies' => $infos['distances'],
                'store' => $store,
            ]);
        }

        //Render options
        $options = [
            'form' => $form->createView(),
            'store' => $store,
            'stores' => $stores = $storeRepository->getAllStores(),
        ];

        if ($store->getReference() !== 'BV001'){
            //Get informations of services
            $infosServices = $serviceSetting->getInfosServices($services, $store);

            //Get infos of companies
            $infosCompanies = $infoSearch->getInfosCompanies($companies, $store);

            $infoOptions = [
                'nbRecommandationsServices' => $infosServices['nbRecommandations'],
                'distancesServices' => $infosServices['distances'],
                'nbRecommandationsCompanies' => $infosCompanies['nbRecommandations'],
                'distancesCompanies' => $infosCompanies['distances'],
                'companies' => $companies,
                'services' => $services,
            ];

            $options = array_merge($options, $infoOptions);
        }

        return $this->render("search/external/searchStore.html.twig", $options);
    }
}
