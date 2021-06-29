<?php

namespace App\Controller;

use App\Form\HomeSearchType;
use App\Form\SearchStoreType;
use App\Repository\CompanyRepository;
use App\Repository\ServiceRepository;
use App\Repository\StoreRepository;
use App\Service\Communities;
use App\Service\GetCompanies;
use App\Service\Search\InfoSearch;
use App\Service\Search\SearchHandler;
use App\Service\ServiceSetting;
use App\Service\Session\ExternalStoreSession;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExternController extends AbstractController
{
    /**
     * A supprimer
     *
     * @Route("/extern/search", name="extern_search")
     */
    public function externSearch(Request $request)
    {
        //Get search form
        $form = $this->createForm(HomeSearchType::class, null);

        $form->handleRequest($request);
        return $this->render("extern/search.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * A supprimer
     *
     * @Route("/extern/service", name="extern_service")
     */
    public function externService(Request $request)
    {
        return $this->render("extern/service.html.twig");
    }

    /**
     * A supprimer
     *
     * @Route("/extern/company", name="extern_company")
     */
    public function externCompany(Request $request)
    {
        return $this->render("extern/company.html.twig");
    }

    /**
     * A supprimer
     *
     * @Route("/extern/all/category", name="extern_all_category")
     */
    public function externAllCategories(Request $request)
    {
        return $this->render("extern/all_categories.html.twig");
    }

    /**
     * A supprimer
     *
     * @Route("/extern/region", name="extern_region")
     */
    public function externRegion(Request $request)
    {
        return $this->render("extern/region.html.twig");
    }

    /**
     * @Route("/homestore", name="homestore", options={"expose"=true})
     */
    public function homeStore(StoreRepository $storeRepository, Communities $communities, ExternalStoreSession $externalStoreSession, Request $request, ServiceRepository $serviceRepository, SearchHandler $searchHandler, CompanyRepository $companyRepository, GetCompanies $getCompanies, InfoSearch $infoSearch, ServiceSetting $serviceSetting)
    {
        //Get store if passed in parameter
        if ($request->get('store'))  $store = $storeRepository->findOneBy(['reference' => $request->get('store')]);

        //Get localisation if passed in parameter
        if ($request->get('locate'))  $locate = $request->get('locate');

        //If not store in params
        if (!isset($store)){
            //Get all stores
            $stores = $storeRepository->getAllStores();

            //If not locate neither
            if (!isset($locate)){
                return $this->render("default/home_store.html.twig", [
                    'store' => null,
                    'stores' => $stores
                ]);
            }

            //Get lat & lon from locate
            $locate = explode(',', $locate);

            //Get closer store form geo-localisation
            $store = $communities->getCloserStore($stores, $locate[0], $locate[1]);
        }

        //Set store ref in session
        $externalStoreSession->setReference($store);

        //Get local services of store
        $allCompanies = $getCompanies->getAllCompanies($store);
        $services = $serviceRepository->findByLocalServicesWithLimit($allCompanies, 12);
        $companies = $companyRepository->findBySearch('', $allCompanies);

        //Get search form
        $form = $this->createForm(SearchStoreType::class, null, ['store' => $store]);

        $form->handleRequest($request);

        //If search
        if ($form->isSubmitted() && $form->isValid()){

            //Get results from searching
            $results = $searchHandler->getResultsExtern($allCompanies, $form->get('querySearch')->getData());

            //Get infos from each company
            $infos = $infoSearch->getInfosCompanies($results);

            //Options rediredct
            $options = [
                'query' => $form->get('querySearch')->getData(),
                'results' => $results,
                'nbRecommandationsCompanies' => $infos['nbRecommandations'],
                'distancesCompanies' => $infos['distances'],
                'store' => $store,
            ];

            return $this->render("search/external/search.html.twig", $options);
        }

        //Get infos of services
        $infosServices = $serviceSetting->getInfosServices($services, $store);

        //Get infos of companies
        $infosCompanies = $infoSearch->getInfosCompanies($companies, $store);

        //Render options
        $options = [
            'form' => $form->createView(),
            'store' => $store,
            'stores' => $stores = $storeRepository->getAllStores(),
            'companies' => $companies,
            'services' => $services,
            'nbRecommandationsServices' => $infosServices['nbRecommandations'],
            'distancesServices' => $infosServices['distances'],
            'nbRecommandationsCompanies' => $infosCompanies['nbRecommandations'],
            'distancesCompanies' => $infosCompanies['distances'],
        ];

        return $this->render("default/home_store.html.twig", $options);
    }
}
