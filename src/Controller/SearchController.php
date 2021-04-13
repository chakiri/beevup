<?php

namespace App\Controller;

use App\Entity\Search;
use App\Entity\User;
use App\Form\SearchType;
use App\Repository\FavoritRepository;
use App\Repository\ServiceRepository;
use App\Service\Search\InfoSearch;
use App\Service\Search\SearchHandler;
use App\Service\ServiceSetting;
use App\Service\User\favorites;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\GetCompanies;

/**
 * @Route("/app/search")
 */
class SearchController extends AbstractController
{
    /**
     * @Route("/", name="search")
     * @Route("/service/{user}", name="search_service_user")
     */
    public function index(Request $request, ?User $user, GetCompanies $getCompanies, favorites $favorites, InfoSearch $infoSearch, SearchHandler $searchHandler, FavoritRepository $favoritRepository, ServiceSetting $serviceSetting, ServiceRepository $serviceRepository)
    {
        $allCompanies = $getCompanies->getAllCompanies( $this->getUser()->getStore());

        //If user, it's mean display all services of user
        if ($user){
            $items = $serviceRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);
        }else{
            $results = $searchHandler->getResults($allCompanies, '');
            $items = $results['items'];

            //Get infos of companies
            $infosCompanies = $infoSearch->getInfosCompanies($results['companies']);
            //Get infos of services
            $infosServices = $serviceSetting->getInfosServices($results['services']);
        }

        $search = new Search();

        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $name = $search->getName();
            //Explode name if containes espace
            if (str_contains($name, ' ')){
                $names_exploded = explode(' ', $name);
                $name = $names_exploded[0];
            }
            $results = $searchHandler->getResults($allCompanies, $name, $search->getIsService(), $search->getIsCompany(), $search->getCategory(), $search->getIsDiscovery());
            $items = $results['items'];
        }

        return $this->render('search/index.html.twig', [
            'SearchForm' => $form->createView(),
            'items' => $items,
            'favoritesUsers' => $favorites->getFavoritesUsers($this->getUser()),
            'favoritesCompanies' => $favorites->getFavoritesCompanies($this->getUser()),
            'favorites' => $favoritRepository->findBy(['user'=> $this->getUser()]),
            'nbRecommandationsServices' => $infosServices['nbRecommandations'] ?? null,
            'distancesServices' => $infosServices['distances'] ?? null,
            'nbRecommandationsCompanies' => $infosCompanies['nbRecommandations'] ?? null,
            'distancesCompanies' => $infosCompanies['distances'] ?? null
        ]);
    }
}
