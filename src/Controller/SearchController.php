<?php

namespace App\Controller;

use App\Entity\Search;
use App\Form\SearchType;
use App\Repository\CategoryRepository;
use App\Repository\CompanyRepository;
use App\Repository\FavoritRepository;
use App\Repository\UserRepository;
use App\Repository\ProfilRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\GetCompanies;


class SearchController extends AbstractController
{

     /**
     * @Route("/search", name="search")
     */


 public function index(Request $request, CompanyRepository $companyRepo, UserRepository $userRepo, UserRepository $useRepo, FavoritRepository $favoritRepo, GetCompanies $getCompanies)
 {
     $search = new Search();
     $allCompanies = $getCompanies->getAllCompanies( $this->getUser()->getStore());
     $form = $this->createForm(SearchType::class, $search);
     $users = $useRepo->findByIsCompletedProfile($allCompanies);
     $companies = null;
     $usersCount = '-1';
     $companiesCount = '-1';
     $favorits = $favoritRepo->findBy(['user'=> $this->getUser()]);
     $favoritUserIds = [];
     $favoritsCompanyIds = [];
     $favoritsNb = count($favorits);

     foreach ($favorits as $favorit)
     {
         array_push( $favoritUserIds, $favorit->getFavoritUser()->getId());
     }
     foreach ($favorits as $favorit)
     {
         if($favorit->getCompany()!= null) {
             array_push($favoritsCompanyIds, $favorit->getCompany()->getId());
         }
     }
     $form->handleRequest($request);
     if($form->isSubmitted() && $form->isValid())
     {

         $data = $form->getData();
         $type = $data->getType();
         $category= $data->getCategory();
         $name = $data->getName();


         if($type == 'company')
         {

             if($category != null && $category !='')
             {
                 if($name =='')
                 {
                     // $companies = $companyRepo->findBy(['category' => $category , 'isCompleted'=>true], []);
                      $companies = $companyRepo->findByCompaniesInCommunity(  $this->getUser()->getStore(), $allCompanies);


                 }
                 else {
                     $companies = $companyRepo->findByValueAndCategory($name, $category, $allCompanies);


                 }
                 $companiesCount = count( $companies);
             } else {

                 $users = null;
                 $companies = $companyRepo->findByValue($name, $allCompanies, $this->getUser()->getStore() );
                 $companiesCount = count( $companies);
             }
         }
         else if($type =='users')
         {

             $users =  $userRepo->findByValue($name,  $allCompanies,  $this->getUser()->getStore());
             $usersCount = count($users);

         }
         else {

             $companies = $companyRepo->findByValue($name, $allCompanies, $this->getUser()->getStore() );
             $users =  $userRepo->findByValue($name, $this->getUser()->getStore());
         }

         return $this->render('search/search.html.twig', [
             'SearchForm' => $form->createView(),
             'users'=> $users ? $users : null,
             'companies'=> $companies ? $companies : null,
             'usersCount' =>   $usersCount,
             'companiesCount' => $companiesCount,
             'favorits' =>  $favorits,
             'favoritUserIds' => $favoritUserIds,
             'favoritsNb' => $favoritsNb,
             'favoritsCompanyIds' => $favoritsCompanyIds

         ]);

     }
     return $this->render('search/search.html.twig', [
         'SearchForm' => $form->createView(),
         'users'=>$users,
         'companies'=> null,
         'favorits' =>  $favorits,
         'favoritUserIds' => $favoritUserIds,
         'favoritsNb' => $favoritsNb


     ]);
 }
}
