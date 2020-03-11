<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    /**
     * @Route("/service", name="service")
     */
    public function index(ServiceRepository $repository)
    {
        $services = $repository->findAll();
        return $this->render('service/home.html.twig', [
            'services' => $services
        ]);
    }

    /**
     * @Route("/service/{id}", name="service_show")
     */
    public function show(Service $service){
        return $this->render('service/show.html.twig', [
            'service' => $service,
        ]);
    }
}
