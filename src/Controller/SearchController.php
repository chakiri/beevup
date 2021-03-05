<?php

namespace App\Controller;

use App\Entity\Search;
use App\Form\SearchType;
use App\Repository\FavoritRepository;
use App\Service\Search\InfoSearch;
use App\Service\Search\SearchHandler;
use App\Service\User\favorites;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\GetCompanies;


class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index(Request $request, GetCompanies $getCompanies, favorites $favorites, InfoSearch $infoSearch, SearchHandler $searchHandler, FavoritRepository $favoritRepository)
    {
        $allCompanies = $getCompanies->getAllCompanies( $this->getUser()->getStore());

        $items = $searchHandler->getResults($allCompanies, '');

        $search = new Search();

        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
            $items = $searchHandler->getResults($allCompanies, $search->getName());

        //Get infos of companies
        $infos = $infoSearch->getInfosCompanies($items);

        return $this->render('search/index.html.twig', [
            'SearchForm' => $form->createView(),
            'items' => $items,
            'nbRecommandations' => $infos['nbRecommandations'],
            'distances' => $infos['distances'],
            'favoritesUsers' => $favorites->getFavoritesUsers($this->getUser()),
            'favoritesCompanies' => $favorites->getFavoritesCompanies($this->getUser()),
            'favorites' => $favoritRepository->findBy(['user'=> $this->getUser()])
        ]);

    }
}
