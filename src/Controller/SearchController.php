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
}
