<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Company;
use App\Repository\PostCategoryRepository;
use App\Repository\FavoritRepository;
use App\Repository\UserRepository;
use App\Repository\RecommandationRepository;
use App\Service\TopicHandler;
use App\Service\BarCode;
use App\Service\Map;
use App\Service\AutomaticPost;
use App\Form\CompanyType;


class CompanyController extends AbstractController
{
    /**
     * @Route("/company/{slug}", name="company_show")
     */
    public function show(Company $company, RecommandationRepository $recommandationRepository, UserRepository $userRepo, FavoritRepository $favoritRepository)
    {
        $users = $userRepo->findBy(['company' => $company, 'isValid' => 1]);
        $adviser= $userRepo->findOneBy(['id'=>$this->getUser()->getStore()->getDefaultAdviser()]);
        $score = 0;
        foreach ($users as $user){
            if ($user->getScore()) $score += $user->getScore()->getPoints();
        }

        $recommandationsServices = $recommandationRepository->findByCompanyServices($company, 'Validated');
        $recommandationsCompany = $recommandationRepository->findByCompanyWithoutServices($company, 'Validated');

        $services = $company->getServices()->toArray();
        $isFavorit = "";
        if (count($favoritRepository->findBy(['user'=> $this->getUser(), 'company'=>$company])) > 0)
        {
            $isFavorit = "is-favorit-profile text-warning";
        }

        return $this->render('company/show.html.twig', [
            'company' => $company,
            'recommandationsServices'=> $recommandationsServices,
            'recommandationsCompany'=> $recommandationsCompany,
            'users' => $users,
            'countServices' => count($services),
            'services' => array_slice($services, -3, 3),
            'score' => $score,
            'isFavorit' => $isFavorit,
            'adviser'=>$adviser,
            'companyAdministrator'=>$userRepo->findByAdminCompany($company->getId())
        ]);
    }

    /**
     * @Route("/company/{id}/edit", name="company_edit")
     */
    public function edit(Company $company, EntityManagerInterface $manager, Request $request, TopicHandler $topicHandler, BarCode $barCode, PostCategoryRepository $postCategoryRepository, AutomaticPost $automaticPost)
    {
        //Denie Access
        if ($this->getUser()->getCompany() == NULL || $company != $this->getUser()->getCompany()) return $this->render('bundles/TwigBundle/Exception/error403.html.twig');

        if ($company == $this->getUser()->getCompany()) {
            $form = $this->createForm(CompanyType::class, $company);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($company->getIsCompleted() == false) {
                    $company->setIsCompleted(true);
                    $category = $postCategoryRepository->findOneBy(['id' => 7]);
                    $automaticPost->Add($this->getUser(), 'Bienvenue à l\'entreprise ' . $company->getName(), '', $category, null, null);
                }

                /* generate bar code*/
                $company->setBarCode($barCode->generate($company->getId()));
                $adresse = $company->getAddressNumber() . ' ' . $company->getAddressStreet() . ' ' . $company->getAddressPostCode() . ' ' . $company->getCity() . ' ' . $company->getCountry();
                $map = new Map();
                $coordonnees = $map->geocode($adresse);

                if ($coordonnees != null) {
                    $company->setLatitude($coordonnees[0]);
                    $company->setLongitude($coordonnees[1]);
                }
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
                'countServices' => count($company->getServices()->toArray())
            ]);
        }
    }

}
