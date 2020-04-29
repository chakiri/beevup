<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use App\Service\InitTopic;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CompanyType;
use App\Repository\RecommandationRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class CompanyController extends AbstractController
{
//    /**
//     * @Route("/company", name="company")
//     */
//    public function index()
//    {
//        return $this->render('company/show.html.twig', [
//            'controller_name' => 'CompanyController',
//        ]);
//    }

    /**
     * @Route("/company/{slug}", name="company_show")
     */
    public function show(Company $company, RecommandationRepository $recommandationRepository, UserRepository $userRepo, ServiceRepository $servicesRepo)
    {
        $recommandations = $recommandationRepository->findBy(['company' => $company->getId(), 'status'=>'Validated'], []);
        $users = $userRepo->findBy(['company' => $company->getId()], []);
        $emailAdmin = $company->getEmail();
        $companyAdmin = $userRepo->findOneBy(['email'=> $emailAdmin],[]);
        $services = $servicesRepo->findBy(['user' => $companyAdmin->getId()], []);
        return $this->render('company/show.html.twig', [
            'company' => $company,
            'recommandations'=> $recommandations,
            'users' => $users,
            'services' => $services
        ]);
    }

    /**
    * @Route("/company/{id}/edit", name="company_edit")
    */
    public function edit(Company $company, EntityManagerInterface $manager, Request $request, InitTopic $initTopic)
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['imageFile']->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename =  $originalFilename;
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('entreprise_logos'),
                        $newFilename
                    );

                } catch (FileException $e) {}
                $company->setLogo($newFilename);
            }

            $company->setIsCompleted(true);
            $manager->persist($company);

            $manager->flush();

            //init topic company category to user
            $initTopic->init($company->getCategory());

           return $this->redirectToRoute('company_show', [
               'slug' => $company->getSlug()
           ]);

        }
        return $this->render('company/form.html.twig', [
            'company' => $company,
            'EditCompanyForm' => $form->createView(),
        ]);
    }
}
