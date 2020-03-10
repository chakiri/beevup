<?php

namespace App\Controller;

use App\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    /**
     * @Route("/company", name="company")
     */
    public function index()
    {
        return $this->render('company/home.html.twig', [
            'controller_name' => 'CompanyController',
        ]);
    }


    /**
     * @Route("/company/{slug}", name="company_show")
     */
    public function show(Company $company)
    {
        return $this->render('company/show.html.twig', [
            'company' => $company
        ]);
    }
}
