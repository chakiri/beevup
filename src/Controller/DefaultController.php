<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Post;
use App\Entity\PostCategory;
use App\Entity\Store;
use App\Entity\Profile;
use App\Form\CompanyImageType;
use App\Form\ProfileImageType;
use App\Form\SearchStoreType;
use App\Form\StoreImageType;
use App\Repository\BeContactedRepository;
use App\Repository\CompanyRepository;
use App\Repository\ProfilRepository;
use App\Repository\PublicityRepository;
use App\Repository\RecommandationRepository;
use App\Repository\StoreRepository;
use App\Repository\UserRepository;
use App\Service\Communities;
use App\Service\Company\CompanySearch;
use App\Service\Dashboard\SpecialOffer;
use App\Service\Error\Error;
use App\Service\GetCompanies;
use App\Service\ImageCropper;
use App\Service\InfoSearch;
use App\Service\Notification\PostNotificationSeen;
use App\Service\ServiceSetting;
use App\Service\Session\ExternalStoreSession;
use App\Service\Session\WelcomePopupSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ServiceRepository;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends AbstractController
{

    /**
     * @Route("/dashboard", name="dashboard")
     * @Route("/dashboard/{category}", name="dashboard_category")
     * @Route("/dashboard/{post}/post", name="dashboard_post")
     * @Route("/dashboard/{category}/load_more/{minId}", name="dashboard_category_load_more")
     * @Route("/dashboard/{post}/post/load_more/{minId}", name="dashboard_post_load_more")
     * @Route("/dashboard/load_more/{minId}", name="dashboard_load_more")
    */
    public function dashboard(PostCategory $category = null, Request $request, Post $post = null, PostRepository $postRepository, PublicityRepository $publicityRepository, PostNotificationSeen $postNotificationSeen, GetCompanies $getCompanies, ServiceRepository $serviceRepository, RecommandationRepository $recommandationRepository, StoreRepository $storeRepository, UserRepository $userRepository, $minId= 0, SpecialOffer $specialOffer, BeContactedRepository $beContactedRepository)
    {
        $store = $this->getUser()->getStore();
        if ($category != null)
            $posts = $postRepository->findByCategory($category, $minId);
        elseif ($post != null) {
            $posts = [];
            if ($post->getUser()->getStore() == $store) {
                $posts[] = $post;
            }
            $postNotificationSeen->set($post);
        }else
            $posts = $postRepository->findByNotReportedPosts($minId);

        $publicity = $publicityRepository->findOneBy([], ['createdAt' => 'DESC']);

        $allCompanies = $getCompanies->getAllCompanies( $this->getUser()->getStore());
        $lastSpecialOffer = $specialOffer->find($allCompanies, $this->getUser()->getStore());

        //Recommandations
        if (in_array('ROLE_ADMIN_STORE', $this->getUser()->getRoles())){
            $untreatedRecommandations = $recommandationRepository->findBy(['store' => $this->getUser()->getStore(), 'status'=>'Open']);
        }elseif (in_array('ROLE_ADMIN_COMPANY', $this->getUser()->getRoles())){
            $untreatedRecommandations = $recommandationRepository->findBy(['company' => $this->getUser()->getCompany(), 'status'=>'Open']);
        }

        //Admin Store
        $currentUserStore = $storeRepository->findOneBy(['id'=>$this->getUser()->getStore()]);
        $adminStore = $userRepository->findByAdminOfStore($currentUserStore, 'ROLE_ADMIN_STORE');

        //Be contacted List of external users
        if (in_array('ROLE_ADMIN_COMPANY', $this->getUser()->getRoles()))
            $beContactedList = $beContactedRepository->findBy(['company' => $this->getUser()->getCompany(), 'isArchived' => false, 'isWaiting' => false]);

        $firstPost =  end($posts);
        $minPostId = ($firstPost == false) ? 'undefined' : $firstPost->getId();

        $options = [
            'posts' => $posts,
            'publicity' => $publicity,
            'lastSpecialOffer' => $lastSpecialOffer,
            'untreatedRecommandations' => $untreatedRecommandations ?? null,
            'adminStore'=> $adminStore[0] ?? null,
            'minPostId' => $minPostId,
            'beContactedList' => $beContactedList ?? null
        ];

        if ($request->get('_route') == 'dashboard_load_more' || $request->get('_route') == 'dashboard_category_load_more' || $request->get('_route') == 'dashboard_post_load_more') {
            return $this->render('default/posts/posts.html.twig', $options);
        }else {
            $options['category'] = $category ? $category->getId() : null;
            return $this->render('default/dashboardv1.html.twig', $options);
        }
    }

    /**
     * @Route("/", name="homepage", options={"expose"=true})
     */
    public function homePage(StoreRepository $storeRepository, Communities $communities, ExternalStoreSession $externalStoreSession, Request $request, ServiceRepository $serviceRepository, ProfilRepository $profilRepository, CompanyRepository $companyRepository, GetCompanies $getCompanies, InfoSearch $infoSearch, CompanySearch $companySearch, ServiceSetting $serviceSetting)
    {
        //Get store if passed in parameter
        if ($request->get('store'))  $store = $storeRepository->findOneBy(['reference' => $request->get('store')]);

        //Get localisation if passed in parameter
        if ($request->get('locate'))  $locate = $request->get('locate');

        //If not store in url
        if (!isset($store)){
            //Get all stores
            $stores = $storeRepository->getAllStores();

            if (!isset($locate)){
                return $this->render("default/home.html.twig", [
                    'store' => null,
                    'stores' => $stores
                ]);
            }

            //Get lat & lon from url
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
            $results = $companySearch->getCompanies($allCompanies, $form->get('querySearch')->getData());

            //Get infos from each company
            $infos = $infoSearch->getInfosCompanies($results, $store);

            //Options redirdct
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

        return $this->render("default/home.html.twig", $options);

    }

    /**
     * @Route()
     */
   /* public function homePage(Request $request, StoreRepository $storeRepository, ServiceRepository $serviceRepository, ProfilRepository $profilRepository, CompanyRepository $companyRepository, GetCompanies $getCompanies, ServiceSetting $serviceSetting, InfoSearch $infoSearch, ExternalStoreSession $externalStoreSession)
    {
        //If store is passed in parameter
        if ($request->get('store'))  $store = $storeRepository->findOneBy(['reference' => $request->get('store')]);
        else    $store = $storeRepository->findOneBy(['reference' => 'BV001']);

        //Redirect if store not found
        if (!$store) return $this->render('bundles/TwigBundle/Exception/error404.html.twig');

        //Set store ref in session
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
            $infos = $infoSearch->getInfosCompanies($results, $store);

            return $this->render("search/external/search.html.twig", [
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

        return $this->render("default/home.html.twig", $options);
    }*/

    /**
     * @Route("/map", name="map")
     */
    public function getClients(StoreRepository $storeRepository, CompanyRepository $companyRepository)
    {
       $stores = $storeRepository->findAll();
       $companies =$companyRepository->findAll();

       $allStores = "";
       $allCompanies="";
       $all = "";

       foreach ($stores as $store)
       {
          if($store->getLatitude() != null && $store->getLongitude() != null ) {
              $adresse = $store->getAddressNumber().' '. $store->getAddressStreet(). ' '.$store->getAddressPostCode();
              $allStores = $allStores . "{\"name\": \"" . $store->getName() . "\", \"lat\": \"" . $store->getLatitude() . "\",\"lng\": \"" . $store->getLongitude() . "\",\"adress\": \"" . $adresse . "\" },";
          }

       }
        foreach ($companies as $company)
        {
            if($company->isValid() === true && $company->getLatitude() != null && $company->getLongitude() != null ) {
                $adresse = $company->getAddressNumber().' '. $company->getAddressStreet(). ' '.$company->getAddressPostCode();
                $allCompanies = $allCompanies. "{\"name\": \"" . $company->getName() . "\", \"lat\": \"" . $company->getLatitude() . "\",\"lng\": \"" . $company->getLongitude() . "\",\"adress\": \"" . $adresse . "\" },";
            }
        }
        $all = $allStores. $allCompanies ;

        $storesJson = rtrim($all, ",");
        return  new Response(
            '{
                       "stores" : [
                        '.$storesJson.'
                      ]
                    }',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    /**
     *  @Route("/welcomePopup", name="welcomepopup")
     */
    public function welcomePopup(WelcomePopupSession $welcomePopupSession)
    {
        $popup = $welcomePopupSession->add();
        return $this->json($popup);
    }

    /**
     * @Route("/company/{id}/updateCompanyImage", name="company_update_image")
     * @Route("/store/{id}/updateStoreImage", name="store_update_image")
     * @Route("/account/{id}/updateProfileImage", name="profile_update_image")
     */
    public function updateImageForm(Request $request,EntityManagerInterface $manager,  Company $company = null, Store $store = null, Profile $profile = null, ImageCropper $imageCropper,  Error $error){

        if ( $request->get('_route') == 'company_update_image') {
            $formType = CompanyImageType::class;
            $entity = $company;
        }
        if ( $request->get('_route') == 'store_update_image'){
            $formType = StoreImageType::class;
            $entity = $store;
        }
        if ( $request->get('_route') == 'profile_update_image'){
            $formType = ProfileImageType::class;
            $entity = $profile;
        }

        $form = $this->createForm( $formType,  $entity);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            if ($form->get('imageFile')->isValid()) {

                $imageCropper->move_directory( $entity);
                $manager->persist( $entity);
                $manager->flush();
                //$this->addFlash('success', 'Vos modifications ont bien été pris en compte !');
                return new JsonResponse( array(
                    'message' => 'Votre photo a été bien modifier',

                ));
            }
            else{
                return new JsonResponse( array(
                    'result' => 0,
                    'message' => 'Invalid form',
                    'data' => $error->getErrorMessages($form)
                ));
            }
        }
        else {
            return $this->render('default/modals/upload/updateImage.html.twig', [
                'ImageForm' => $form->createView(),
                'entity' =>  $entity,
            ]);
        }
    }

}
