<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Post;
use App\Entity\PostCategory;
use App\Entity\Store;
use App\Entity\Profile;
use App\Form\CompanyImageType;
use App\Form\ProfileImageType;
use App\Form\StoreImageType;
use App\Repository\BeContactedRepository;
use App\Repository\CompanyRepository;
use App\Repository\PublicityRepository;
use App\Repository\RecommandationRepository;
use App\Repository\StoreRepository;
use App\Repository\UserRepository;
use App\Service\Dashboard\SpecialOffer;
use App\Service\Email;
use App\Service\Error\Error;
use App\Service\GetCompanies;
use App\Service\ImageCropper;
use App\Service\Notification\PostNotificationSeen;
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
            if($company->getLatitude() != null && $company->getLongitude() != null ) {
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

    /**
     * @Route("/test_sendinblue", name="test_sendinblue")
     */
    public function testSendiblue(Email $email)
    {

        //$email->createContactApi('yassir.chakiri12@gmail.com');

        dd($email->getContactsApi());
        //$email->sendEmailForTemplate();

        return $this->redirectToRoute('dashboard');

    }

}
