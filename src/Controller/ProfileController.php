<?php

namespace App\Controller;

use App\Form\CompanyType;
use App\Form\InscriptionAccountType;
use App\Repository\RecommandationRepository;
use App\Repository\ServiceRepository;
use App\Repository\FavoritRepository;
use App\Repository\UserRepository;
use App\Service\Company\CompanySetting;
use App\Service\Mail\ContactsHandler;
use App\Service\ImageCropper;
use App\Service\TopicHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Profile;
use App\Form\ProfileType;

/**
 * @Route("/app")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/account/{id}", name="profile_show")
     */
    public function show(Profile $profile, ServiceRepository $serviceRepository, RecommandationRepository $recommandationRepository, FavoritRepository $favoritRepository, UserRepository $userRepo)
    {
        $collegues = null;
        $services = $serviceRepository->findBy(['user' => $profile->getUser()], ['createdAt' => 'DESC']);
        $favoritUser= $userRepo->findBy(['id'=>$profile->getUser()]);
        if($profile->getUser()->getCompany() != null) {
            $collegues = $userRepo->findBy(['company' => $profile->getUser()->getCompany(), 'isValid'=> 1],[], 5);
        }

        $isFavorit = "";
       if (count($favoritRepository->findBy(['user'=> $this->getUser(), 'favoritUser'=>$favoritUser])) > 0)
            {
                $isFavorit = "is-favorit-profile text-warning";
            }

        $allRecommandations = $recommandationRepository->findByUserRecommandation($profile->getUser(), 'Validated');

        return $this->render('profile/show.html.twig', [
            'profile' => $profile,
            'services' => array_slice($services, 0, 3),
            'countServices' => count($services),
            'recommandations' => $allRecommandations,
            'isFavorit' => $isFavorit,
            'collegues' =>$collegues
        ]);
    }

    /**
     * @Route("/account/{id}/edit", name="profile_edit")
     */
    public function form(Profile $profile, EntityManagerInterface $manager, Request $request, TopicHandler $topicHandler , ImageCropper $imageCropper, ContactsHandler $contactsHandler)
    {
        //Denied access
        if($profile->getUser() !== $this->getUser()) return $this->render('bundles/TwigBundle/Exception/error403.html.twig');

        //Profile form
        $formProfile = $this->createForm(ProfileType::class, $profile);
        $formProfile->handleRequest($request);

        if ($formProfile->isSubmitted() && $formProfile->isValid()) {

            //Cropped Image
            $imageCropper->move_directory($profile);

            //Set profile is completed
            $profile->setIsCompleted(true);

            $manager->persist($profile);
            $manager->flush();

            //Add topic function to user type 2
            if ($profile->getUser()->getType()->getId() === 2)
                $topicHandler->initFunctionStoreTopic($profile->getUser());

            //Create new contact on SendinBlue
            $contactsHandler->handleContactSendinBlueCompleteProfile($this->getUser());

            $this->addFlash('success', 'Vos modifications ont bien été prises en compte !');

            return $this->redirectToRoute('profile_show', [
                'id' => $profile->getId()
            ]);
        }

        return $this->render('profile/form.html.twig', [
            'EditProfileForm' => $formProfile->createView(),
            'profile' => $profile,
        ]);

    }

    /**
     * @Route("/onboarding", name="onboarding")
     */
    public function setOnBoarding(EntityManagerInterface $manager)
    {
        $profile = $this->getUser()->getProfile();

        if ($_POST['value'] === "true")     $profile->setIsOnboarding(true);
        else $profile->setIsOnboarding(false);
        $manager->persist($profile);
        $manager->flush();
        return $this->json($profile);
    }

}
