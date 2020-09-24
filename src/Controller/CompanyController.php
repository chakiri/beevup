<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Post;
use App\Entity\UserType;
use App\Repository\PostCategoryRepository;
use App\Repository\FavoritRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use App\Service\TopicHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\CompanyType;
use App\Repository\RecommandationRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\BarCode;
use App\Service\Map;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class CompanyController extends AbstractController
{

    /**
     * @Route("/company/{slug}", name="company_show")
     */
    public function show(Company $company, RecommandationRepository $recommandationRepository, UserRepository $userRepo, FavoritRepository $favoritRepository,  ServiceRepository $servicesRepo)
    {
        $recommandations = $recommandationRepository->findBy(['company' => $company, 'status'=>'Validated']);
        $users = $userRepo->findBy(['company' => $company, 'isValid' => 1]);
        $adviser= $userRepo->findOneBy(['id'=>$this->getUser()->getStore()->getDefaultAdviser()]);
        $score = 0;
        foreach ($users as $user){
            if ($user->getScore()) $score += $user->getScore()->getPoints();
        }

        $services = $company->getServices()->toArray();
        $isFavorit = "";
        if (count($favoritRepository->findBy(['user'=> $this->getUser(), 'company'=>$company])) > 0)
        {
            $isFavorit = "is-favorit-profile text-warning";
        }

        return $this->render('company/show.html.twig', [
            'company' => $company,
            'recommandations'=> $recommandations,
            'users' => $users,
            'countServices' => count($services),
            'services' => array_slice($services, 0, 3),
            'score' => $score,
            'isFavorit' => $isFavorit,
            'adviser'=>$adviser
        ]);
    }

    /**
     * @Route("/company/{id}/edit", name="company_edit")
     */
    public function edit(Company $company, EntityManagerInterface $manager, Request $request, TopicHandler $topicHandler, BarCode $barCode, UserTypeRepository $userTypeRepository, UserRepository $userRepository, $id, PostCategoryRepository $postCategoryRepository)
    {
        if ($this->getUser()->getCompany() != NULL) {
            if ($id == $this->getUser()->getCompany()->getId()) {
                $form = $this->createForm(CompanyType::class, $company);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {

                   if($company->getIsCompleted() == false) {
                       $company->setIsCompleted(true);
                       // create a new welcome post
                       $AdminPLatformeType = $userTypeRepository->findOneBy(['id' =>5]);
                       //$user = $userRepository->findOneBy(['type'=>$AdminPLatformeType]);
                       $category = $postCategoryRepository->findOneBy(['id' => 7]);
                       $post = new Post();
                       $post->setUser($this->getUser());
                       $post->setCategory($category);
                       $post->setTitle('Bienvenue à l\'entreprise '.$company->getName());
                       $post->setDescription($company->getIntroduction());

                       $post->setToCompany($company);
                       $manager->persist($post);
                   }

                    /* generate bar code*/
                    $company->setBarCode($barCode->generate($company->getId()));
                    $adresse = $company->getAddressNumber().' '.$company->getAddressStreet().' '.$company->getAddressPostCode().' '.$company->getCity().' '.$company->getCountry();
                    $map = new Map();
                    $coordonnees =  $map->geocode($adresse);
                 
                    if($coordonnees !=null) {
                        $company->setLatitude($coordonnees[0]);
                        $company->setLongitude($coordonnees[1]);
                    }
                    /* end ******/
                    $manager->persist($company);



                    $manager->flush();

                    //init topic company category to user
                    $topicHandler->initCategoryCompanyTopic($company->getCategory());

                    $this->addFlash('success', 'Vos modifications ont bien été pris en compte !');

                    return $this->redirectToRoute('company_show', [
                        'slug' => $company->getSlug()
                    ]);

                }
                return $this->render('company/form.html.twig', [
                    'company' => $company,
                    'EditCompanyForm' => $form->createView(),
                ]);
            } else {
                return $this->redirectToRoute('page_not_found', []);
            }
        }
    else{
        return $this->redirectToRoute('page_not_found', []);
    }
    }
}
