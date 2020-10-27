<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Search;
use App\Entity\User;
use App\Form\SearchType;
use App\Repository\CompanyRepository;
use App\Repository\FavoritRepository;
use App\Repository\RecommandationRepository;
use App\Repository\UserRepository;
use App\Service\Communities;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\GetCompanies;


class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index(Request $request, GetCompanies $getCompanies, UserRepository $userRepository, FavoritRepository $favoritRepository, CompanyRepository $companyRepository, RecommandationRepository $recommandationRepository, Communities $communities)
    {
        $allCompanies = $getCompanies->getAllCompanies( $this->getUser()->getStore());

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        $items = $userRepository->findByIsCompletedProfile($allCompanies);

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

        //Get infos from each item
        $nbRecommandations = [];
        $distances = [];
        foreach ($items as $item){
            if ($item instanceof User){
                $company = $item->getCompany();
                //Get nb recommandations of each company item
                $nbRecommandation = count($recommandationRepository->findByUserRecommandation($item, 'Validated'));
            }
            elseif ($item instanceof Company){
                $company = $item;
                //Get nb recommandations of each company item
                $nbRecommandation = count($recommandationRepository->findByCompanyServices($company, 'Validated')) + count($recommandationRepository->findByCompanyWithoutServices($company, 'Validated'));
            }
            $nbRecommandations[$company->getId()] = $nbRecommandation;
            //Get nb Km between current user company and company item
            $distance = $communities->calculateDistanceBetween($company, $this->getUser()->getCompany(), 'K');
            $distances[$company->getId()] = $distance;
        }

        return $this->render('search/search.html.twig', [
            'SearchForm' => $form->createView(),
            'items' => $items,
            'favoris' => $favoris,
            'favorisUsersIds' => $favorisUsersIds,
            'favorisCompaniesIds' => $favorisCompaniesIds,
            'nbRecommandations' => $nbRecommandations,
            'distances' => $distances,
            'isUser' => $items ? $items[0] instanceof User : false,
        ]);

    }
}
