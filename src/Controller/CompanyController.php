<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Post;
use App\Entity\UserType;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class CompanyController extends AbstractController
{

    /**
     * @Route("/company/{slug}", name="company_show")
     */
    public function show(Company $company, RecommandationRepository $recommandationRepository, UserRepository $userRepo, ServiceRepository $servicesRepo)
    {
        $recommandations = $recommandationRepository->findBy(['company' => $company, 'status'=>'Validated']);
        $users = $userRepo->findBy(['company' => $company]);
        $adviser= $userRepo->findOneBy(['id'=>$this->getUser()->getStore()->getDefaultAdviser()]);
        $score = 0;
        foreach ($users as $user){
            if ($user->getScore()) $score += $user->getScore()->getPoints();
        }

        $services = $company->getServices()->toArray();

        return $this->render('company/show.html.twig', [
            'company' => $company,
            'recommandations'=> $recommandations,
            'users' => $users,
            'countServices' => count($services),
            'services' => array_slice($services, 0, 3),
            'score' => $score,
            'adviser'=>$adviser
        ]);
    }

    /**
     * @Route("/company/{id}/edit", name="company_edit")
     */
    public function edit(Company $company, EntityManagerInterface $manager, Request $request, TopicHandler $topicHandler, BarCode $barCode, $id, UserRepository $userRepository, UserType $userType)
    {
        if ($this->getUser()->getCompany() != NULL) {
            if ($id == $this->getUser()->getCompany()->getId()) {
                $form = $this->createForm(CompanyType::class, $company);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {

                   if($company->getIsCompleted() == false) {
                       $company->setIsCompleted(true);
                       // create a new welcome post
                       $AdminPLatformeType = $userType->findOneBy(['id' =>5]);
                       $user = $userRepository->findOneBy(['type'=>$AdminPLatformeType]);
                       $post = new Post();
                       $post->setUser($user);
                       $post->setCategory('Derniers arrivés');
                       $post->setTitle('Bienvenu à l\'entreprise '.$company->getName());
                       $post->setDescription($company->getDescription());
                       $post->setToCompany($company);
                       $manager->persist($post);
                   }

                    /* generate bar code*/
                    $company->setBarCode($barCode->generate($company->getId()));
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
