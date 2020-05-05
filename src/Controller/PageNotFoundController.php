<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PageNotFoundController extends AbstractController
{
    /**
     * @Route("/404", name="page_not_found")
     */
    public function index()
    {
        return $this->render('page_not_found/index.html.twig', [
            'controller_name' => 'PageNotFoundController',
        ]);
    }
}
