<?php

namespace App\Controller;

use App\Repository\ProfilRepository;
use App\Repository\RecommandationRepository;
use App\Repository\ServiceRepository;
use App\Repository\FavoritRepository;
use App\Repository\UserRepository;
use App\Service\TopicHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Profile;
use App\Form\ProfileType;

class ProfileController extends AbstractController
{
    /**
     * @Route("/account/{id}", name="profile_show")
     */
    public function show(Profile $profile, ServiceRepository $serviceRepository, RecommandationRepository $recommandationRepository, FavoritRepository $favoritRepository, UserRepository $userRepo, ProfilRepository $profileRepo, $id)
    {
        $collegues = null;
        $services = $serviceRepository->findBy(['user' => $profile->getUser()], ['createdAt' => 'DESC']);
        $favoritUser= $userRepo->findBy(['id'=>$profile->getUser()]);
        if($profile->getUser()->getCompany() != null) {
            $collegues = $userRepo->findBy(['company' => $profile->getUser()->getCompany()],[], 5);

        }


        $isFavorit = "";
       if (count($favoritRepository->findBy(['user'=> $this->getUser(), 'favoritUser'=>$favoritUser])) > 0)
            {
                $isFavorit = "is-favorit-profile text-warning";
            }

        $allRecommandations = [];
        //dd($services);
        foreach ($services as $service){
            $recommandations = $recommandationRepository->findBy(['service' => $service]);
            if ($recommandations)
                $allRecommandations = array_merge($allRecommandations, $recommandations);
        }

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
    public function form(Profile $profile, EntityManagerInterface $manager, Request $request, TopicHandler $topicHandler)
    {

        if($profile->getUser() == $this->getUser()) {
            $form = $this->createForm(ProfileType::class, $profile);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $profile->setIsCompleted(true);

                $manager->persist($profile);
                $manager->flush();

                /* Add topic function to user type 2 */
                if ($profile->getUser()->getType()->getId() == 2)
                    $topicHandler->initFunctionStoreTopic($profile->getUser());

                return $this->redirectToRoute('profile_show', [
                    'id' => $profile->getId()
                ]);
            }

            return $this->render('profile/form.html.twig', [
                'EditProfileForm' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('page_not_found', []);
        }
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
