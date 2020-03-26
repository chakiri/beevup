<?php

namespace App\Controller;

use App\Entity\Company;
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
    /**
    * @Route("/company", name="company")
    */

    public function index()
    {
        return $this->render('company/show.html.twig', [
            'controller_name' => 'CompanyController',
        ]);
    }

    /**
    * @Route("/company/{slug}", name="company_show")
    */

    public function show(Company $company, RecommandationRepository $recommandationRepository)
    {
        $recommandations = $recommandationRepository->findBy(['company' => $company->getId(), 'status'=>'Validated'], []);
        return $this->render('company/show.html.twig', [
            'company' => $company,
            'recommandations'=> $recommandations
        ]);
    }

    /**
    * @Route("/company/{id}/edit", name="company_edit")
    */

    public function edit(Company $company, EntityManagerInterface $manager, Request $request)
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

           return $this->redirectToRoute('company_show', [
               'slug' => $company->getSlug()
           ]);

        }
        return $this->render('company/edit.html.twig', [
            'company' => $company,
            'EditCompanyForm' => $form->createView(),
        ]);
    }
}
