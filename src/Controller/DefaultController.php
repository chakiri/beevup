<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostCategory;
use App\Repository\AbuseRepository;
use App\Repository\CompanyRepository;
use App\Repository\NotificationRepository;
use App\Repository\OpportunityNotificationRepository;
use App\Repository\PublicityRepository;
use App\Repository\RecommandationRepository;
use App\Repository\StoreRepository;
use App\Repository\UserRepository;
use App\Service\Session\WelcomePopup;
use Faker\Provider\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ServiceRepository;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Repository\PostsNotificationRepository;
use App\Repository\PostLikeRepository;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{

    /**
     * @Route("/dashboard/old", name="dashboard")
     */
    public function dashboard(ServiceRepository $repository, RecommandationRepository $recommandationRepository, PostRepository $postRepository, CommentRepository $CommentRepository, PostLikeRepository $postLikeRepository, PostsNotificationRepository $dashboardNotificationRepository, NotificationRepository $notificationRepository, OpportunityNotificationRepository $opportunityNotificationRepo, StoreRepository $storeRepo, UserRepository $userRepo, AbuseRepository $abuseRepository, PublicityRepository $publicityRepo)
    {
        $services = $repository->findBy(['user' => $this->getUser()->getId()], [], 3);
        $specialOfferNb = count($repository->findBy(['isDiscovery' => 1]));
        $lastSpecialOffer = $repository->findOneBy(['isDiscovery'=> 1 ],['createdAt' => 'DESC']);
        $posts = $postRepository->findByNotReportedPosts();
        $currentUserStore = $storeRepo->findOneBy(['id'=>$this->getUser()->getStore()]);
       // $adviser= $userRepo->findOneBy(['id'=>$currentUserStore->getDefaultAdviser()]);
        $adminsStore = $userRepo->findByAdminOfStore($currentUserStore, 'ROLE_ADMIN_STORE');


        $OpportunityPostsIds = [''];
        $publicity =  $publicityRepo->findOneBy([],['createdAt'=>'DESC']);

         /****************************** opportunity notifications****/
         $displayedOpportunityPosts =$opportunityNotificationRepo->findByLastMonthNotification($this->getUser());
            foreach ($displayedOpportunityPosts as $post ) {
              array_push($OpportunityPostsIds , $post->getPost()->getId());
           }
         $opportunityPostNb = count($postRepository->findByNotSeenOpportunityPost("Opportunité commerciale", $OpportunityPostsIds, $this->getUser()));
        $currentUser =$this->getUser();
        $likedPost = [];
        $reportedPosts = [];
        $reportedComment = [];
        $untreatedCompanyRecommandationsNumber = 0;
        $untreatedServiceRecommandationsNumber = 0;
        $companyRecommandations = [];
        foreach ($posts as $post){
            $result = $postLikeRepository->findOneByPostAndUser($currentUser, $post->getId());
            $isReported = $abuseRepository->findOneBy(['post'=>$post->getId(), 'user'=>$this->getUser()]);
            if($result != null) {
                array_push($likedPost, 1);
            } else {
                array_push($likedPost, 0);
            }

            // to check if the post is already reported by the current user
            if($isReported != null) {
                array_push($reportedPosts, 1);
            } else {
                array_push($reportedPosts, 0);
            }
            
        }
        if($this->getUser()->getCompany() != null) {
            $companyRecommandations = $recommandationRepository->findBy(['company' => $this->getUser()->getCompany()->getId(), 'status' => 'Validated'], []);
            $untreatedCompanyRecommandations = $recommandationRepository->findBy(['company' => $this->getUser()->getCompany()->getId(), 'status'=>'Open'], []);
            $untreatedCompanyRecommandationsNumber = count($untreatedCompanyRecommandations);
        }


        $serviceRecommandationToBeTraited = $recommandationRepository->findByUserRecommandation($this->getUser(), 'Open');
        $untreatedServiceRecommandationsNumber = count($serviceRecommandationToBeTraited);

        $totalUntraitedRecommandation = $untreatedCompanyRecommandationsNumber + $untreatedServiceRecommandationsNumber;
        $postNumber = count($posts);
        $comments = $CommentRepository->findByNotReportedComment();
        foreach($comments as $comment)
        {
            $isReported = $abuseRepository->findOneBy(['comment'=>$comment->getId(), 'user'=>$this->getUser()]);
            if($isReported != null) {
                array_push($reportedComment, 1);
            } else {
                array_push($reportedComment, 0);
            }
        }
        $dashboardNotifications = $dashboardNotificationRepository->findByDistinctPostAndType($this->getUser());
        $notificationNumber = count($dashboardNotifications);

        //Get notification chat
        $notificationMessages = $notificationRepository->findMessageNotifs($this->getUser());

        return $this->render('default/dashboard.html.twig', [
            'services' => $services,
            'posts'   => $posts,
            'recommandations' => array_merge($companyRecommandations, $serviceRecommandationToBeTraited),
            'untreatedRecommandationsNumber' =>$totalUntraitedRecommandation,
            'postNumber' => $postNumber,
            'comments' => $comments,
            'likedPost' => $likedPost,
            'dashboardNotifications'=>$dashboardNotifications,
            'notificationNumber'=>$notificationNumber,
            'notificationMessages' => $notificationMessages,
            'opportunityPostNb'  => $opportunityPostNb,
            'specialOfferNb'=>$specialOfferNb,
            'lastSpecialOffer'=>$lastSpecialOffer,
            'adminStore'=> $adminsStore[0] ?? null,
            'reportedPosts'=>$reportedPosts,
            'reportedComments'=>$reportedComment,
            'publicity'=> $publicity
        ]);
    }

    /**
     * @Route("/dashboard", name="dashboard")
     * @Route("/dashboard/{category}", name="dashboard_category")
     */
    public function dashboardv1(PostCategory $category = null, PostRepository $postRepository)
    {
        if ($category != null)
            $posts = $postRepository->findBy(['status' => null, 'category' => $category], ['createdAt' => 'DESC']);
        else
            $posts = $postRepository->findByNotReportedPosts();


        return $this->render('default/dashboardv1.html.twig', [
            'posts' => $posts
        ]);
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
    public function welcomePopup(WelcomePopup $welcomePopup)
    {
        $popup = $welcomePopup->add();

        return $this->json($popup);
    }


}
