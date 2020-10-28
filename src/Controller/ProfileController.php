<?php

namespace App\Controller;

use App\Repository\PostCategoryRepository;
use App\Repository\ProfilRepository;
use App\Repository\RecommandationRepository;
use App\Repository\ServiceRepository;
use App\Repository\FavoritRepository;
use App\Repository\UserRepository;
use App\Service\AutomaticPost;
use App\Service\ImageCropper;
use App\Service\TopicHandler;
use App\Service\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Profile;
use App\Form\ProfileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;


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
    public function form(Profile $profile,PostCategoryRepository $postCategoryRepository, EntityManagerInterface $manager, Request $request, TopicHandler $topicHandler, AutomaticPost $autmaticPost, ImageCropper $imageCropper, Utility $utility)
    {
        if($profile->getUser() == $this->getUser()) {
            $form = $this->createForm(ProfileType::class, $profile);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $isCompleted = $profile->getIsCompleted();
                $imageCropper->move_directory($profile, 'uploads_dir');

                if(!$isCompleted){
                    /*******Add automatic post***/
                     $category = $postCategoryRepository->findOneBy(['id' => 7]);
                     $description = $profile->getFirstname() ?? 'Pour plus d\'information, visitez le profil de ' . $profile->getFirstname();
                     $autmaticPost->Add("Bienvenue au ". $profile->getFirstname() . " ". $profile->getLastname(), $description, $category, $profile->getId(), 'User');
                }
                $profile->setFirstname($utility->updateName($profile->getFirstname()));
                $profile->setLastname($utility->updateName($profile->getLastname()));
                $profile->setIsCompleted(true);

                $manager->persist($profile);
                $manager->flush();

                /* Add topic function to user type 2 */
                if ($profile->getUser()->getType()->getId() == 2)
                    $topicHandler->initFunctionStoreTopic($profile->getUser());
                $this->addFlash('success', 'Vos modifications ont bien été pris en compte !');
                return $this->redirectToRoute('profile_show', [
                    'id' => $profile->getId()
                ]);
            }

            return $this->render('profile/form.html.twig', [
                'EditProfileForm' => $form->createView(),
            ]);
        }else {
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

    /**
     * @Route("/uploade-image", name="image", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getImage(Request $request)
    {

        if ($request->isXmlHttpRequest())
        {

            $profile = new Profile();
            $profile->setUser($this->getUser());
            $form = $this->createForm(ProfileType::class, $profile);
            $form->handleRequest($request);
            // the file
            $file = $_FILES['file'];
            $file = new UploadedFile($file['tmp_name'], $file['name'], $file['type']);
            $filename = $this->generateUniqueName() . '.' . $file->guessExtension();
            $file->move(
                $this->getTargetDir(),
                $filename
            );
            $profile->setFilename($filename);
            $em = $this->getDoctrine()->getManager();
            $em->persist($profile);
            $em->flush();
        }
        return new JsonResponse("This is not an ajax request");
    }

    private function generateUniqueName()
    {
        return md5(uniqid());
    }

    private function getTargetDir()
    {
        return $this->getParameter('uploads_dir');
    }
}
