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
use App\Service\Communities;
use App\Repository\UserTypeRepository;
use App\Service\ScoreHandler;
use App\Service\ServiceSetting;
use App\Service\GetCompanies;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function index($user = null, $company = null, $store = null, Request $request, ServiceRepository $serviceRepository, TypeServiceRepository $typeServiceRepository, StoreRepository $storeRepository, UserRepository $userRepository, CompanyRepository $companyRepository, GetCompanies $getCompanies, RecommandationRepository $recommandationRepository, Communities $communities, StoreServicesRepository $storeServicesRepository)
    {
        $allCompanies = $getCompanies->getAllCompanies($this->getUser()->getStore());
        $services = $serviceRepository->findByLocalServices($allCompanies);

        //Add related generic services of store
        $storeServices = $this->getUser()->getStore()->getServices();
        foreach ($storeServices as $storeService){
            array_push($services, $storeService->getService());
        }

        if ($request->get('_route') == 'service_generic'|| $this->isGranted('ROLE_ADMIN_PLATEFORM')) {
            $typeService = $typeServiceRepository->findOneBy(['name' => 'plateform']);
            $services = $serviceRepository->findByType($typeService);
        }
        if ($request->get('_route') == 'service_discovery') {
            $services = $serviceRepository->findByIsDiscovery($allCompanies);
            //Add services of store
            foreach ($storeServices as $storeService){
                if ($storeService->getService()->getIsDiscovery() == true) array_push($services, $storeService->getService());
            }
        }
        if ($company){
            $company = $companyRepository->findOneBy(['id' => $company]);
            $services =[];
            if (in_array($company->getId(), $allCompanies)) {
                $services = $company->getServices();
            }
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
            $services = $serviceRepository->findSearch($query, $category, $isDiscovery, $allCompanies);
            //Add services of store
            foreach ($storeServices as $storeService){
                if ($isDiscovery == true){
                    if ($storeService->getService()->getIsDiscovery() == true) array_push($services, $storeService->getService());
                }else{
                    array_push($services, $storeService->getService());
                }
            }
            $user = null;
        }

        //Get advisor of store
        $adviser= $userRepository->findOneBy(['id'=>$this->getUser()->getStore()->getDefaultAdviser()]);

        //Get informations of services
        $nbRecommandations = [];
        $distances = [];
        foreach ($services as $service){
            if ($service->getType()->getName() == 'company') {
                //Get nb recommandations of each company
                $nbRecommandation = count($recommandationRepository->findBy(['company' => $company = $service->getUser()->getCompany(), 'service' => $service]));
                $nbRecommandations[$service->getId()] = $nbRecommandation;
                //Get nb Km between current user company and company service
                if ($this->getUser()->getCompany()){
                    $distance = $communities->calculateDistanceBetween($this->getUser()->getCompany(), $company, 'K');
                    $distances[$service->getId()] = $distance;
                }
            }elseif ($service->getType()->getName() == 'store'){
                $nbRecommandation = count($recommandationRepository->findBy(['store' => $store = $service->getUser()->getStore(), 'service' => $service]));
                $nbRecommandations[$service->getId()] = $nbRecommandation;
                if ($this->getUser()->getCompany()) {
                    $distance = $communities->calculateDistanceBetween($this->getUser()->getCompany(), $store, 'K');
                    $distances[$service->getId()] = $distance;
                }
            }elseif ($service->getType()->getName() == 'plateform'){
                //Get assocaition if exist
                $storeService = $storeServicesRepository->findOneBy(['service' => $service, 'store' => $this->getUser()->getStore()]);
                if ($storeService){
                    $nbRecommandation = count($recommandationRepository->findBy(['store' => $store = $storeService->getStore(), 'service' => $service]));
                    $nbRecommandations[$service->getId()] = $nbRecommandation;
                    if ($this->getUser()->getCompany()){
                        $distance = $communities->calculateDistanceBetween($this->getUser()->getCompany(), $store, 'K');
                        $distances[$service->getId()] = $distance;
                    }
                }
            }
        }

        return $this->render('service/index.html.twig', [
            'services' => $services,
            'distances' => $distances,
            'nbRecommandations' => $nbRecommandations,
            'isPrivate' => isset($user),
            'isDiscovery' => $request->get('_route') == 'service_discovery',
            'adviser'=> $adviser,
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
     * @Route("/service/{id}/edit", name="service_edit")
     * @Route("/service/new/{isOffer}", name="service_new")
     */
    public function form(?Service $service, $isOffer = false, Request $request, EntityManagerInterface $manager, ServiceSetting $serviceSetting, ScoreHandler $scoreHandler)
    {
        if($service != null) {
            if ($request->get('_route') == 'service_edit' && $service->getUser()->getId() != $this->getUser()->getId()) {
                return $this->redirectToRoute('page_not_found', []);
            }
        }

        $message = 'Votre Service a bien été mis à jour !';
        if (!$service){
            $service = new Service();
            $url = $this->generateUrl('service_new');
            $message = "Votre Service a bien été crée ! <a href='$url'>Créer un nouveau service</a>";
            $service->setUser($this->getUser());
        }
        $form = $this->createForm(ServiceType::class, $service, array('isOffer'=>$isOffer));
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if (!$service->getType())
                //Set type depending on user role
                $serviceSetting->setType($service);

            $serviceSetting->setToParent($service);

            $optionsRedirect = [];
            //Add score to user if creation
            if ($service->getIsDiscovery()){
                $nbPoints = 20;
                if (!$service->getId()){
                    $scoreHandler->add($this->getUser(), $nbPoints);
                    $optionsRedirect = ['toastScore' => $nbPoints];
                }
            }
            $service->setPrice( $this->floatvalue($service->getPrice()));
            $manager->persist($service);
            $manager->flush();

            //Merge score option to options array
            $optionsRedirect = array_merge($optionsRedirect, ['id' => $service->getId()]);

            $this->addFlash('success', $message);

            return $this->redirectToRoute('service_show', $optionsRedirect);
        }
        return $this->render('service/form.html.twig', [
            'service' => $service,
            'ServiceForm' => $form->createView(),
            'edit' => $service->getId() != null,
        ]);
    }

    /**
    * @Route("/service/{id}", name="service_show")
    */
    public function show(Service $service, ServiceRepository $serviceRepository, RecommandationRepository $recommandationRepository, CompanyRepository $companyRepository, StoreServicesRepository $storeServicesRepository, UserRepository $userRepository, UserTypeRepository $userTypeRepository, GetCompanies $getCompanies, $id )
    {
        $company = null;
        $companyId = null;
        $allCompanies = $getCompanies->getAllCompanies($this->getUser()->getStore());


        //Get store Service if it's an association
        $storeService = $storeServicesRepository->findOneBy(['store' => $this->getUser()->getStore(), 'service' => $service]);
        if ($storeService){
            //Get admin store from store of this association
            $adminType = $userTypeRepository->findOneBy(['name' => 'admin magasin']);
            $adminStoreService = $userRepository->findOneBy(['store' => $storeService->getStore(), 'type' => $adminType]);
        }

        //$similarServices = $serviceRepository->findBy(['category' => $service->getCategory()], [], 3);
        $similarServices = $serviceRepository->findByCategory($service->getCategory(), $allCompanies, $id);

        $recommandations = $recommandationRepository->findBy(['service' => $service, 'status'=>'Validated']);
        $recommandationsCompany = $recommandationRepository->findBy(['company' => $company, 'service' => null, 'status'=>'Validated']);

        return $this->render('service/show.html.twig', [
            'service' => $service,
            'storeService' => $storeService,
            'adminStoreService' => $adminStoreService ?? null,
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

    function floatvalue($val){
        $val = str_replace(",",".",$val);
        $val = preg_replace('/\.(?=.*\.)/', '', $val);
        return floatval($val);
    }

}
