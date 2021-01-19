<?php

namespace App\Controller;

use App\Entity\BeContacted;
use App\Entity\Store;
use App\Form\BeContactedType;
use App\Repository\BeContactedRepository;
use App\Repository\CompanyRepository;
use App\Service\Chat\AutomaticMessage;
use App\Service\Mailer;
use App\Service\GetCompanies;
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
                    $automaticPost->Add($this->getUser(), 'Bienvenue à l\'entreprise ' . $company->getName(), '', $category, null, null, null, $company);
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
                    'slug' => $company->getSlug(),
                    'id' => $company->getId(),
                ]);

            }
            return $this->render('company/form.html.twig', [
                'company' => $company,
                'EditCompanyForm' => $form->createView(),
                'countServices' => count($company->getServices()->toArray())
            ]);
        }
    }

    /**
     * @Route("/company/{slug}/{id}", name="company_show", requirements={"slug"="[a-zA-Z0-9\-_\/]+", "id"="\d+"})
     */
    public function show(Company $company, RecommandationRepository $recommandationRepository, UserRepository $userRepo, FavoritRepository $favoritRepository)
    {
        $users = $userRepo->findBy(['company' => $company, 'isValid' => 1]);
        if ($this->getUser()) $adviser= $userRepo->findOneBy(['id'=>$this->getUser()->getStore()->getDefaultAdviser()]);
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
            'services' => array_slice($services, -6, 6),
            'score' => $score,
            'isFavorit' => $isFavorit,
            'adviser'=>$adviser ?? null,
            'companyAdministrator'=>$userRepo->findByAdminCompany($company->getId())
        ]);
    }


    /**
     * @Route("/external/company/slider/{reference}", name="external_company_slider")
     */
    public function externalSlider(Store $store, GetCompanies  $getCompanies, CompanyRepository $companyRepository, UserRepository  $userRepository)
    {
        //Get local companies of store
        $allCompanies = $getCompanies->getAllCompanies($store);
        $companies = $companyRepository->findBySearch('', $allCompanies);

        //Get admin of each company
        $admins = [];
        $servicesArray = [];
        foreach ($companies as $company){
            $admin = $userRepository->findByAdminCompany($company->getId());
            $admins[$company->getId()] = $admin;
            $services = $company->getServices()->toArray();
            $servicesArray[$company->getId()] = array_slice($services, -3, 3);
        }

        return $this->render('company/external/slider.html.twig', [
            'store' => $store,
            'companies' => $companies,
            'admins' => $admins,
            'servicesArray' => $servicesArray
        ]);
    }

    /**
     * @Route("/external/company/{slug}/{id}", name="external_company_show")
     */
    public function externalShow(Request $request, Company $company, RecommandationRepository $recommandationRepository, UserRepository $userRepository, BeContactedRepository  $beContactedRepository, EntityManagerInterface $manager, Mailer $mailer, AutomaticMessage $automaticMessage)
    {
        $recommandationsServices = $recommandationRepository->findByCompanyServices($company, 'Validated');
        $recommandationsCompany = $recommandationRepository->findByCompanyWithoutServices($company, 'Validated');

        $services = $company->getServices()->toArray();

        $users = $userRepository->findBy(['company' => $company]);

        $admin = $userRepository->findByAdminCompany($company->getId());

        $beContacted = new BeContacted();
        $form = $this->createForm(BeContactedType::class, $beContacted);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            if ($beContactedRepository->findBy(['company' => $company, 'email' => $beContacted->getEmail(), 'isArchived' => false])){
                $this->addFlash('warning', 'Une demande envoyé le ' . $beContacted->getCreatedAt()->format('d/m/Y') . ' est toujours en cours. '. $company->getName() . ' vous contactera  très prochainement.');
            } else{
                $beContacted->setCompany($company);
                $manager->persist($beContacted);
                $manager->flush();

                //Send email to external user
                $mailer->sendEmail('Votre demande de contact sur le site Beevup.fr', $beContacted->getEmail(), ['company' => $company, 'beContacted' => $beContacted], 'confirmBeContacted.html.twig');


                // add chat message to sponsor
                if ($admin)
                    $automaticMessage->fromAdvisorToUser($admin, 'Bonne nouvelle !<br> Un client souhaite être contacté. <a href="' . $this->generateUrl('be_contacted_list', ['status' => 'current']) . '">Voir la liste des demandes</a>');

                $this->addFlash('success', $company->getName() . ' a été notifiée et reviendra vers vous dans les plus brefs délais');
            }
            return $this->redirectToRoute('external_company_show', [
                'slug' => $company->getSlug(),
                'id' => $company->getId(),
            ]);
        }

        return $this->render('company/external/show.html.twig', [
            'company' => $company,
            'recommandationsServices'=> $recommandationsServices,
            'recommandationsCompany'=> $recommandationsCompany,
            'countServices' => count($services),
            'services' => array_slice($services, -6, 6),
            'users' => $users,
            'admin' => $admin,
            'formBeContacted' => $form->createView()
        ]);
    }

}
