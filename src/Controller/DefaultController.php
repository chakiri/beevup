<?php

namespace App\Controller;

use App\Entity\DashboardNotification;
use App\Entity\PostLike;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Repository\OpportunityNotificationRepository;
use App\Repository\RecommandationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ServiceRepository;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Repository\DashboardNotificationRepository;
use App\Repository\PostLikeRepository;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('default/home.html.twig', [

        ]);
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(ServiceRepository $repository, RecommandationRepository $recommandationRepository, PostRepository $postRepository, CommentRepository $CommentRepository, PostLikeRepository $postLikeRepository, DashboardNotificationRepository $dashboardNotificationRepository, NotificationRepository $notificationRepository, OpportunityNotificationRepository $opportunityNotificationRepo)
    {
        $services = $repository->findBy(['user' => $this->getUser()->getId()], [], 3);
        $specialOfferNb = count($repository->findBy(['isDiscovery' => 1]));
        $posts = $postRepository->findBy([], array('createdAt' => 'DESC'));



        /****************************** opportunity notifications****/
            $OpportunityPostsIds = [''];
            //$displayedOpportunityPosts = $opportunityNotificationRepo->findBy(['user'=>$this->getUser()],[]);
            $displayedOpportunityPosts =$opportunityNotificationRepo->findByLastMonthNotification($this->getUser());


           foreach ($displayedOpportunityPosts as $post )
           {
            array_push($OpportunityPostsIds , $post->getPost()->getId());
           }
           $opportunityPost = $postRepository->findByNotSeenOpportunityPost("Opportunities", $OpportunityPostsIds);

           $opportunityPostNb = count($opportunityPost);

           /**********************************************************************/


        $currentUser =$this->getUser();
        $likedPost = [];
        $untreatedCompanyRecommandationsNumber = 0;
        $untreatedServiceRecommandationsNumber = 0;
        $companyRecommandations = [];
        foreach ($posts as $post){
            $result = $postLikeRepository->findOneByPostAndUser($currentUser, $post->getId());
            if($result != null) {
               array_push($likedPost, 1);
            } else {
                array_push($likedPost, 0);
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
        $comments = $CommentRepository->findBy([], []);
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
            'specialOfferNb'=>$specialOfferNb
        ]);
    }
}
