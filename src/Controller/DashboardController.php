<?php


namespace App\Controller;


use App\Entity\Label;
use App\Entity\Post;
use App\Entity\PostCategory;
use App\Form\KbisType;
use App\Repository\BeContactedRepository;
use App\Repository\ExpertMeetingRepository;
use App\Repository\LabelRepository;
use App\Repository\PostRepository;
use App\Repository\PublicityRepository;
use App\Repository\RecommandationRepository;
use App\Repository\StoreRepository;
use App\Repository\UserRepository;
use App\Service\Dashboard\SpecialOffer;
use App\Service\Error\Error;
use App\Service\ExpertMeeting\GetExpertMeeting;
use App\Service\GetCompanies;
use App\Service\Mail\Mailer;
use App\Service\Notification\PostNotificationSeen;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/app/dashboard", name="dashboard")
     * @Route("app/dashboard/{category}", name="dashboard_category")
     * @Route("/app/dashboard/{post}/post", name="dashboard_post")
     */
    public function dashboard(PostCategory $category = null, Request $request, Post $post = null, PostRepository $postRepository, PublicityRepository $publicityRepository, PostNotificationSeen $postNotificationSeen, GetCompanies $getCompanies, RecommandationRepository $recommandationRepository, StoreRepository $storeRepository, UserRepository $userRepository, SpecialOffer $specialOffer, BeContactedRepository $beContactedRepository, GetExpertMeeting $getExpertMeeting, ExpertMeetingRepository $expertMeetingRepository)
    {
        $store = $this->getUser()->getStore();
        if ($category)
            $posts = $postRepository->findByCategory($category);
        elseif ($post) {
            $posts = [];
            if ($post->getUser()->getStore() === $store) {
                $posts[] = $post;
            }
            $postNotificationSeen->set($post);
        }else
            $posts = $postRepository->findByNotReportedPosts();

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

        $options = [
            'posts' => $posts,
            'publicity' => $publicity,
            'lastSpecialOffer' => $lastSpecialOffer,
            'untreatedRecommandations' => $untreatedRecommandations ?? null,
            'adminStore'=> $adminStore[0] ?? null,
            'beContactedList' => $beContactedList ?? null,
            'status' => $request->get('status') ?? null,
            'category' => $category ? $category->getId() : null
        ];

        //Get list expert meetings
        $optionsExpertsMeetings = $getExpertMeeting->list($allCompanies);
        //Get only 3 elements of array
        $optionsExpertsMeetings['expertsMeetings'] = array_slice($optionsExpertsMeetings['expertsMeetings'], 0, 3);

        return $this->render('dashboard/dashboardv1.html.twig', array_merge($options, $optionsExpertsMeetings));
    }

    /**
     * @Route("/modal/charter", name="modal_charter", options={"expose"=true})
     */
    public function modalSignCharter()
    {
        return $this->render('dashboard/modals/charter.html.twig');
    }

    /**
     * @IsGranted("ROLE_ADMIN_COMPANY")
     * @Route("/sign/charter", name="sign_charter", options={"expose"=true})
     */
    public function signCharter(EntityManagerInterface $manager, LabelRepository $labelRepository, Mailer $mailer)
    {
        $company = $this->getUser()->getCompany();

        $label = $labelRepository->findOneBy(['company' => $company]);

        if (!$label){
            $label = new Label();
            $label->setCompany($company);
        }

        $label->setCharter(true);

        $manager->persist($label);

        $manager->flush();

        $params = [
            'url' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'name' => $this->getUser()->getProfile()->getFullName(),
        ];
        $mailer->sendEmailWithTemplate($this->getUser()->getEmail(), $params, 'label_chart_signed');

        return $this->json([
            'message' => 'charter signed'
        ], 200);
    }

    /**
     * Ajax handle upload kbisFile in popup
     * @IsGranted("ROLE_ADMIN_COMPANY")
     * @Route("/upload/kbis", name="upload_kbis", options={"expose"=true})
     */
    public function modalKbisForm(Request $request, EntityManagerInterface $manager, Error $error, LabelRepository $labelRepository)
    {
        $company = $this->getUser()->getCompany();

        $label = $labelRepository->findOneBy(['company' => $company]);

        if (!$label){
            $label = new Label();
            $label->setCompany($company);
        }

        $form = $this->createForm(KbisType::class, $label);

        $form->handleRequest($request);

        if ($form->isSubmitted()){
            if ($form->get('kbisFile')->isValid()){
                //Get file from ajax FormData
                $file = $request->files->get('kbis')['kbisFile'];

                $status = ['status' => "success", "message" => 'file not uploaded'];

                // If a file was uploaded
                if($file){
                    $label->setKbisFile($file);
                    $label->setKbisStatus('isWaiting');

                    $manager->persist($label);
                    $manager->flush();

                    $status = ['status' => "success", "message" => 'file uploaded'];
                }
            }else{
                $status = ['status' => "error", "message" => $error->getErrorMessages($form->get('kbisFile'))];
            }

            return $this->json($status);
        }

        return $this->render('dashboard/modals/kbisForm.html.twig', [
            'form' => $form->createView()
        ]);
    }
}