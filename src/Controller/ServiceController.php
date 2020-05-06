<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceSearchType;
use App\Form\ServiceType;
use App\Repository\RecommandationRepository;
use App\Repository\ServiceRepository;
use App\Repository\CompanyRepository;
use App\Repository\StoreRepository;
use App\Repository\StoreServicesRepository;
use App\Repository\TypeServiceRepository;
use App\Repository\UserRepository;
use App\Service\HandleScore;
use App\Service\ServiceSetting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ServiceController extends AbstractController
{

    /**
    * @Route("/service", name="service")
    * @Route("/service/generic", name="service_generic")
    * @Route("/service/discovery", name="service_discovery")
    * @Route("/service/company/{company}", name="service_company")
    * @Route("/service/store/{store}", name="service_store")
    * @Route("/service/user/{user}", name="service_user")
    */
    public function index($user = null, $company = null, $store = null, Request $request, ServiceRepository $serviceRepository, TypeServiceRepository $typeServiceRepository, StoreRepository $storeRepository, UserRepository $userRepository, CompanyRepository $companyRepository, StoreServicesRepository $storeServicesRepository)
    {
        $services = $serviceRepository->findBy([], ['createdAt' => 'DESC', 'isDiscovery' => 'DESC']);
        $currentUserStore = $storeRepository->findOneBy(['id'=>$this->getUser()->getStore()]);
        $adviser= $userRepository->findOneBy(['id'=>$currentUserStore->getDefaultAdviser()]);

        if ($request->get('_route') == 'service_discovery') {
            $services = $serviceRepository->findBy(['isDiscovery' => 1], ['createdAt' => 'DESC']);
        }
        if ($request->get('_route') == 'service_generic') {
            $typeService = $typeServiceRepository->findOneBy(['name' => 'plateform']);
            $services = $serviceRepository->findBy(['type' => $typeService], ['createdAt' => 'DESC']);
        }
        if ($company){
            $company = $companyRepository->findOneBy(['id' => $company]);
            $services = $company->getServices();
        }
        if ($store){
            $store = $storeRepository->findOneBy(['id' => $store]);
            $services = [];
            $storeServices = $store->getServices();
            foreach ($storeServices as $service){
                array_push($services, $service->getService());

            }
        }

        if ($user) {
            $user = $userRepository->findBy(['id' => $user]);
            $services = $serviceRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);
        }

        $searchForm = $this->createForm(ServiceSearchType::class);

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted()){
            $query = $searchForm->get('query')->getData();
            $category = $searchForm->get('category')->getData();
            $isDiscovery = $searchForm->get('isDiscovery')->getData();

            $services = $serviceRepository->findSearch($query, $category, $isDiscovery);

            $user = null;
        }

        return $this->render('service/index.html.twig', [
            'services' => $services,
            'isPrivate' => isset($user),
            'isDiscovery' => $request->get('_route') == 'service_discovery',
            'adviser'=> $adviser,
            'store'=>$currentUserStore,
            'searchForm' => $searchForm->createView()
        ]);
    }

    /**
     * @Route("/service/{service}/associate", name="service_associate")
     */
    public function associate(Service $service, EntityManagerInterface $manager, StoreRepository $storeRepository, ServiceSetting $serviceSetting)
    {
        if ($service && $service->getType()->getName() == 'plateform'){

            $store = $storeRepository->findOneBy(['id' => $this->getUser()->getStore()]);

            //New StoreService entity
            $storeService = $serviceSetting->setStoreService($store, $service);
            $store->addService($storeService);

            $manager->persist($store);
            $manager->flush();

            $this->addFlash('success', 'Ce service a bien été ajouté à vos propositions !');

            return $this->redirectToRoute('service_store', [
                'store' => $this->getUser()->getStore()->getId()
            ]);
        }
    }

    /**
     * @Route("/service/{id}/dissociate", name="service_dissociate")
     */
    public function dissociate(Service $service, EntityManagerInterface $manager, StoreRepository $storeRepository, StoreServicesRepository $storeServicesRepository)
    {
        if ($service && $service->getType()->getName() == 'plateform'){

            $store = $storeRepository->findOneBy(['id' => $this->getUser()->getStore()]);

            //Get ServiceStore
            $storeService = $storeServicesRepository->findOneBy(['store' => $store, 'service' => $service]);

            $store->removeService($storeService);

            $manager->persist($store);
            $manager->flush();

            $this->addFlash('success', 'Ce service a été retiré de vos propositions !');

            return $this->redirectToRoute('service_store', [
                'store' => $this->getUser()->getStore()->getId()
            ]);
        }
    }

    /**
     * @IsGranted("ROLE_ADMIN_COMPANY")
     * @Route("/service/{id}/edit", name="service_edit")
     * @Route("/service/new", name="service_new")
     */
    public function form(?Service $service, Request $request, EntityManagerInterface $manager, ServiceSetting $serviceSetting, HandleScore $handleScore)
    {
         if($service != null) {
            if ($request->get('_route') == 'service_edit' && $service->getUser()->getId() != $this->getUser()->getId()) {
                return $this->redirectToRoute('page_not_found', []);
            }
        }
        $message = 'Votre Service a bien été mis à jour !';
        if (!$service){
            $service = new Service();
            $message = "Votre Service a bien été crée !";
            $service->setUser($this->getUser());
        }
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if (!$service->getType())
                //Set type depending on user role
                $serviceSetting->setType($service);

            $serviceSetting->setToParent($service);

            //Add score to user if creation
            if ($service->getIsDiscovery())
                if (!$service->getId()) $handleScore->handle($this->getUser(), 20);

            $manager->persist($service);
            $manager->flush();

            $this->addFlash('success', $message);

            return $this->redirectToRoute('service_show', [
                'id' => $service->getId()
            ]);
        }
        return $this->render('service/form.html.twig', [
            'service' => $service,
            'ServiceForm' => $form->createView(),
            'edit' => $service->getId() != null
        ]);
    }

    /**
    * @Route("/service/{id}", name="service_show")
    */
    public function show(Service $service, ServiceRepository $serviceRepository, RecommandationRepository $recommandationRepository, CompanyRepository $companyRepository, StoreServicesRepository $storeServicesRepository)
    {
        $company = null;

        if(!is_null($service->getUser()->getCompany())) {
            $company = $companyRepository->findOneById($service->getUser()->getCompany()->getId());
            $companyId = $company->getId();
        }

        $storeService = $storeServicesRepository->findOneBy(['store' => $this->getUser()->getStore(), 'service' => $service]);

        $similarServices = $serviceRepository->findBy(['category' => $service->getCategory()], [], 3);

        $recommandations = $recommandationRepository->findBy(['service' => $service, 'status'=>'Validated']);

        $recommandationsCompany = $recommandationRepository->findBy(['company' => $company, 'service' => null, 'status'=>'Validated']);

        return $this->render('service/show.html.twig', [
            'service' => $service,
            'storeService' => $storeService,
            'companyId'  => $companyId,
            'similarServices' => $similarServices,
            'recommandations'=> $recommandations,
            'recommandationsCompany'=> $recommandationsCompany,
        ]);
    }

    /**
     * @Route("/service/{id}/remove", name="service_remove")
     */
    public function remove(Service $service, EntityManagerInterface $manager)
    {
        if ($service){
            $manager->remove($service);
            $manager->flush();

            $this->addFlash('success', 'Le service a bien été supprimé !');
        }

        return $this->redirectToRoute('service');
    }

    /**
     * @Route("/service/{id}/config", name="service_config")
     */
    public function configAssociation(Service $service, Request $request, StoreServicesRepository $storeServicesRepository, EntityManagerInterface $manager)
    {
        $price = $request->get('price');

        if ($service){
            $storeService = $storeServicesRepository->findOneBy(['store' => $this->getUser()->getStore(), 'service' => $service]);
            $storeService->setPrice($price);

            $manager->persist($storeService);

            $manager->flush();

            $this->addFlash('success', 'Votre prix a bien été pris en compte !');

            return $this->redirectToRoute('service_show', [
                'id' => $service->getId()
            ]);
        }
    }
}
