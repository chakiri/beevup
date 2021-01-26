<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Recommandation;
use App\Form\RecommandationType;
use App\Repository\CompanyRepository;
use App\Repository\PostCategoryRepository;
use App\Repository\ServiceRepository;
use App\Repository\RecommandationRepository;
use App\Repository\StoreRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use App\Service\AutomaticPost;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RecommandationController extends AbstractController
{
   
    /**
     * @Route("/recommandations", name="recommandations")
     */
    public function index(RecommandationRepository $recommandationRepository)
    {
        if ($this->getUser()->getType()->getId() == 1){
            $recommandations = $recommandationRepository->findBy(['store' => $this->getUser()->getStore(), 'status'=>'Open']);
        }elseif ($this->getUser()->getType()->getId() == 3){
            $recommandations = $recommandationRepository->findBy(['company' => $this->getUser()->getCompany(), 'status'=>'Open']);
        }

        return $this->render('recommandation/index.html.twig', [
            'profile' => $this->getUser()->getProfile(),
            'recommandations' => $recommandations
        ]);
    }

    /**
     * @Route("/recommandation", name="recommandation")
     */
    public function create(Request $request, EntityManagerInterface $manager, ServiceRepository $serviceRepository, CompanyRepository $companyRepository, StoreRepository $storeRepository, UserRepository $userRepository, UserTypeRepository $userTypeRepository, Mailer $mailer)
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
                $storePatron = $userRepository->findOneBy(['type'=> $userTypePatron, 'store'=> $store, 'isValid'=>1]);
                $messageFlash ='Merci pour votre proposition de recommandation, le responsable de magasin '.$store->getName().'  a été notifié et va pouvoir valider votre message';
            }elseif ($company){
                $recommandation->setCompany($company);
                $userTypeAdminCompany =  $userTypeRepository->findOneBy(['id'=> 3]);
                $admin = $userRepository->findOneBy(['type'=> $userTypeAdminCompany, 'company'=> $company]);
                $storePatron = $userRepository->findOneBy(['type'=> $userTypePatron, 'store'=> $company->getStore(), 'isValid'=>1]);
                $messageFlash ='Merci pour votre proposition de recommandation, le responsable de l\'entreprise '.$company->getName().'  a été notifié et va pouvoir valider votre message';
            }

            $manager->persist($recommandation);
            $manager->flush();
            //$mailer->sendEmail('Beev\'Up par Bureau Vallée - Un autre membre vous a recommandé', $admin->getEmail(), ['user'=> $admin, 'storePatron'=> $storePatron], 'recommandation.html.twig');
            $mailer->sendEmailWithTemplate($admin->getEmail(), ['url' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL)], 5);

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
                    'slug' => $company->getSlug(),
                    'id' => $company->getId()
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
    public function edit(Request $request, EntityManagerInterface $manager, RecommandationRepository $repository, PostCategoryRepository $postCategoryRepository, AutomaticPost $autmaticPost, $variable, $variable2)
    {
        $recommandation = $repository->findOneById($variable2);
        $status = (1 == $variable) ? 'Validated' : 'Rejected';
        $recommandation->setStatus($status);
        //============Add automatic post ============
        if($variable == 1){

            $category = $postCategoryRepository->findOneBy(['id' => 8]);
            $autmaticPost->Add($this->getUser(), 'Nouvelle Recommandation' ,  $recommandation->getMessage(), $category, $recommandation->getId(), 'Recommandation', $recommandation);

        }
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