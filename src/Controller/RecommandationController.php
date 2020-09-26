<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Recommandation;
use App\Form\RecommandationType;
use App\Repository\CompanyRepository;
use App\Repository\ServiceRepository;
use App\Repository\RecommandationRepository;
use App\Repository\StoreRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
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
    public function index(RecommandationRepository $recommandationRepository)
    {
        if ($this->getUser()->getType()->getName() == 'admin magasin'){
            $recommandations = $recommandationRepository->findBy(['store' => $this->getUser()->getStore(), 'status'=>'Open']);
        }elseif ($this->getUser()->getType()->getName() == 'admin entreprise'){
            $recommandations = $recommandationRepository->findBy(['company' => $this->getUser()->getCompany(), 'status'=>'Open']);
        }

        return $this->render('recommandation/index.html.twig', [
            'recommandations' => $recommandations
        ]);
    }

    /**
     * @Route("/recommandation", name="recommandation")
     */
    public function create(Request $request, EntityManagerInterface $manager, ServiceRepository $serviceRepository, CompanyRepository $companyRepository, StoreRepository $storeRepository, UserRepository $userRepository, UserTypeRepository $userTypeRepository, \Swift_Mailer $mailer)
    {
        $recommandation = new Recommandation();

        $form = $this->createForm(RecommandationType::class, $recommandation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            //Get all data from form
            $serviceId = $form->get("service")->getData();
            $companyId = $form->get("company")->getData();
            $storeId   = $form->get("store")->getData();

            //Get Objects
            $service = $serviceRepository->findOneBy(['id' => $serviceId]);
            $company = $companyRepository->findOneBy(['id' => $companyId]);
            $store   = $storeRepository->findOneBy(['id' => $storeId]);

            $recommandation->setUser($this->getUser());
            if ($service){
                $recommandation->setService($service);
            }

            //Get Patron of Store/Company
            $userTypePatron = $userTypeRepository->findOneBy(['id'=> 4]);
            if ($store){
                $recommandation->setStore($store);
                $userTypeAdminStore =  $userTypeRepository->findOneBy(['id'=> 1]);
                $admin = $userRepository->findOneBy(['type'=> $userTypeAdminStore, 'store'=> $store]);
                $storePatron = $userRepository->findOneBy(['type'=> $userTypePatron, 'store'=> $store]);
                $messageFlash ='Merci pour votre proposition de recommandation, le responsable de magasin '.$store->getName().'  a été notifié et va pouvoir valider votre message';
            }elseif ($company){
                $recommandation->setCompany($company);
                $userTypeAdminCompany =  $userTypeRepository->findOneBy(['id'=> 3]);
                $admin = $userRepository->findOneBy(['type'=> $userTypeAdminCompany, 'company'=> $company]);
                $storePatron = $userRepository->findOneBy(['type'=> $userTypePatron, 'store'=> $company->getStore()]);
                $messageFlash ='Merci pour votre proposition de recommandation, le responsable de l\'entreprise '.$company->getName().'  a été notifié et va pouvoir valider votre message';
            }

            $manager->persist($recommandation);
            $manager->flush();

            $message = (new \Swift_Message())
                ->setSubject('Beev\'Up par Bureau Vallée - Un autre membre vous a recommandé')
                ->setFrom($_ENV['DEFAULT_EMAIL'])
                ->setTo($admin->getEmail())
                ->setBody(
                    $this->renderView('emails/recommandation.html.twig',
                        ['user'=> $admin, 'storePatron'=> $storePatron]
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);

            $this->addFlash('success', $messageFlash);

            if ($service){
                return $this->redirectToRoute('service_show', [
                    'id' => $service->getId()
                ]);
            }elseif ($store){
                return $this->redirectToRoute('store_show', [
                    'slug' => $store->getSlug()
                ]);
            }elseif ($company){
                return $this->redirectToRoute('company_show', [
                    'slug' => $company->getSlug()
                ]);
            }
        }


        return $this->render('recommandation/form.html.twig', [
            'RecommandationForm' => $form->createView(),
        ]);
    }
    
    
    /**
     * @Route("/recommandation1", name="recommandation1")
     */
    public function create1(Request $request, EntityManagerInterface $manager, CompanyRepository $companyRepository, ServiceRepository $serviceRepository,UserRepository $userRepository, UserTypeRepository $userTypeRepository, \Swift_Mailer $mailer, StoreRepository $storeRepository)
    {
      $recommandation = new Recommandation();
     
      $form = $this->createForm(RecommandationType::class, $recommandation);
      $form->handleRequest($request);
     
      if ($form->isSubmitted() && $form->isValid()) {
        $companyId = $form->get('companyId')->getData();
        $serviceId = $form->get('serviceId')->getData();
        $isAssociated = $form->get('isAssociated')->getData();
        $associatedStoreId = $form->get('associatedStore')->getData();

        $company = $companyRepository->findOneById($companyId);
        $service = $serviceRepository->findOneById($serviceId);

        if($company != null && $isAssociated == 0) {
          $recommandation->setCompany($company);
          $this->addFlash('success', 'Merci pour votre proposition de recommandation, le responsable de l\'entreprise '.$company->getName().'  a été notifié et va pouvoir valider votre message');

         }
        if ($service != null)
        {
          $recommandation->setService($service);
        }
         $recommandation->setUser( $this->getUser());
         $manager->persist($recommandation);
         $manager->flush();

         /**** send email ******/
          $userTypePatron = $userTypeRepository->findOneBy(['id'=> 4]);
          $userTypeAdmin =  $userTypeRepository->findOneBy(['id'=> 3]);
          $userTypeAdminStore =  $userTypeRepository->findOneBy(['id'=> 1]);
          $storePatron = $userRepository->findOneBy(['type'=> $userTypePatron, 'store'=> $company->getStore()]);
          $adminCompany = $userRepository->findOneBy(['type'=> $userTypeAdmin, 'company'=>$company]);

          //If not company get admin store unstead
          $associatedStore = $storeRepository->findOneBy(['id' => $associatedStoreId]);
          if ($adminCompany == null){
              $adminCompany = $userRepository->findOneBy(['type'=> $userTypeAdminStore, 'store'=> $associatedStore]);
          }

          $message = (new \Swift_Message())
              ->setSubject('Beev\'Up par Bureau Vallée - Un autre membre vous a recommandé')
              ->setFrom($_ENV['DEFAULT_EMAIL'])
              ->setTo($adminCompany->getEmail())
              ->setBody(
                  $this->renderView(
                      'emails/recommandation.html.twig',
                      ['user'=> $adminCompany, 'storePatron'=> $storePatron]
                  ),
                  'text/html'
              )
          ;
          $result = $mailer->send($message);
         /*****end *************/

         if ( $service != null && $company != null) {
             $this->addFlash('success', 'Merci pour votre proposition de recommandation, '.$service->getUser()->getProfile()->getFirstname().' '.$service->getUser()->getProfile()->getLastname().'  a été notifié et va pouvoir valider votre message');

             return $this->redirectToRoute('service_show', [
                'id' => $service->getId()
            ]);
         }

          if($company != null) {
              $this->addFlash('success', 'Merci pour votre proposition de recommandation, le responsable de l\'entreprise '.$company->getName().'  a été notifié et va pouvoir valider votre message');

              return $this->redirectToRoute('company_show', [
                  'slug' => $company->getSlug()
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