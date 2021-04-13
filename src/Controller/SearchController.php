<?php

namespace App\Controller;

use App\Entity\Search;
use App\Form\SearchType;
use App\Repository\FavoritRepository;
use App\Service\Search\InfoSearch;
use App\Service\Search\SearchHandler;
use App\Service\ServiceSetting;
use App\Service\User\favorites;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\GetCompanies;

/**
 * @Route("/app")
 */
class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index(Request $request, GetCompanies $getCompanies, favorites $favorites, InfoSearch $infoSearch, SearchHandler $searchHandler, FavoritRepository $favoritRepository, ServiceSetting $serviceSetting)
    {
        $allCompanies = $getCompanies->getAllCompanies( $this->getUser()->getStore());

        $results = $searchHandler->getResults($allCompanies, '');
        $items = $results['items'];

        $search = new Search();

        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $results = $searchHandler->getResults($allCompanies, $search);
            $items = $results['items'];
        }

        //Get infos of companies
        $infosCompanies = $infoSearch->getInfosCompanies($results['companies']);
        //Get infos of services
        $infosServices = $serviceSetting->getInfosServices($results['services']);

        return $this->render('search/index.html.twig', [
            'SearchForm' => $form->createView(),
            'items' => $items,
            'favoritesUsers' => $favorites->getFavoritesUsers($this->getUser()),
            'favoritesCompanies' => $favorites->getFavoritesCompanies($this->getUser()),
            'favorites' => $favoritRepository->findBy(['user'=> $this->getUser()]),
            'nbRecommandationsServices' => $infosServices['nbRecommandations'],
            'distancesServices' => $infosServices['distances'],
            'nbRecommandationsCompanies' => $infosCompanies['nbRecommandations'],
            'distancesCompanies' => $infosCompanies['distances']
        ]);

    }
}
