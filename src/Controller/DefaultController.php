<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ServiceRepository;
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('default/home.html.twig', [

        ]);
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(ServiceRepository $repository)
    {
        $services = $repository->findBy(['user' => $this->getUser()->getId()], [], 3);
        return $this->render('default/dashboard.html.twig', [
            'services' => $services
        ]);
    }
}
