<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Recommandation;
use App\Form\RecommandationType;
use App\Repository\CompanyRepository;
use App\Repository\ServiceRepository;
use App\Repository\RecommandationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class RecommandationController extends AbstractController
{
   
    /**
     * @Route("/recommandations", name="recommandations")
     */
    public function index(RecommandationRepository $repository)
    {
        $companyRecommandations = $repository->findBy(['company' => $this->getUser()->getCompany()->getId(), 'status'=>'Open'], []);
        $serviceRecommandation = $repository->findByUserRecommandation($this->getUser(), 'Open');
        return $this->render('recommandation/index.html.twig', [
            'controller_name' => 'RecommandationController',
            'recommandations' => array_merge($companyRecommandations,$serviceRecommandation)
        ]);
    }
    
    
    /**
     * @Route("/recommandation", name="recommandation")
     */
    public function create(Request $request, EntityManagerInterface $manager, CompanyRepository $companyRepository, ServiceRepository $serviceRepository)
    {
     
      $recommandation = new Recommandation();
     
      $form = $this->createForm(RecommandationType::class, $recommandation);
      $form->handleRequest($request);
     
      if ($form->isSubmitted() && $form->isValid()) {
        $companyId = $form->get('companyId')->getData();
        $serviceId = $form->get('serviceId')->getData();
        $company = $companyRepository->findOneById($companyId);
        $service = $serviceRepository->findOneById($serviceId);
        
        if($company != null) {
          $recommandation->setCompany($company);
          $this->addFlash('success', 'Merci pour votre proposition de recommandation, le responsable de l\'entreprise '.$company->getName().'  a été notifié et va pouvoir valider votre message');

         }
        if ( $service != null)
        {
          $recommandation->setService($service);
          $this->addFlash('recommandation-success', 'Merci pour votre proposition de recommandation, '.$service->getUser()->getProfile()->getFirstname().' '.$service->getUser()->getProfile()->getLastname().'  a été notifié et va pouvoir valider votre message');

        }
        $recommandation->setUser( $this->getUser());
         $manager->persist($recommandation);
         $manager->flush();
         if($company != null) {
         return $this->redirectToRoute('company_show', [
             'slug' => $company->getSlug()
         ]);
         }
         if ( $service != null && $company != null) {
            return $this->redirectToRoute('service_show', [
                'id' => $service->getId()
            ]);

         }
      }
      return $this->render('recommandation/form.html.twig', [
          'RecommandationForm' => $form->createView(),
         
      ]);
    }

     /**
     * @Route("/edit/recommandation/{variable}/{variable2}", defaults={"variable" = 0, "variable2" = 0}, name="recommandation_edit")
     */
    public function edit(Request $request, EntityManagerInterface $manager, RecommandationRepository $repository, $variable, $variable2)
    {
        
        
        $recommandation = $repository->findOneById($variable2);
        $status = (1 == $variable) ? 'Validated' : 'Rejected';
        $recommandation->setStatus($status);
        $manager->persist($recommandation);
        $manager->flush();
        $response = new Response(
            'Content',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
        return $response;
    }


}