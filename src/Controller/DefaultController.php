<?php

namespace App\Controller;

use App\Repository\RecommandationRepository;
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
    public function dashboard(ServiceRepository $repository, RecommandationRepository $recommandationRepository)
    {
        $services = $repository->findBy(['user' => $this->getUser()->getId()], [], 3);

        $companyRecommandations = $recommandationRepository->findBy(['company' => $this->getUser()->getCompany()->getId(), 'status'=>'Validated'], []);
        
        
        $untreatedCompanyRecommandations = $recommandationRepository->findBy(['company' => $this->getUser()->getCompany()->getId(), 'status'=>'Open'], []);
        $untreatedCompanyRecommandationsNumber = count($untreatedCompanyRecommandations);

        $serviceRecommandationToBeTraited = $recommandationRepository->findByUserRecommandation($this->getUser(), 'Open');
        $untreatedServiceRecommandationsNumber = count($serviceRecommandationToBeTraited);

        $totalUntraitedRecommandation = $untreatedCompanyRecommandationsNumber + $untreatedServiceRecommandationsNumber;
        
        return $this->render('default/dashboard.html.twig', [
            'services' => $services,
            'recommandations' => array_merge($companyRecommandations, $serviceRecommandationToBeTraited),
            'untreatedRecommandationsNumber' =>$totalUntraitedRecommandation
        ]);
    }
}
