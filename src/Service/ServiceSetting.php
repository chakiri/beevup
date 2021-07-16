<?php

namespace App\Service;


use App\Entity\Service;
use App\Entity\Store;
use App\Entity\StoreService;
use App\Repository\RecommandationRepository;
use App\Repository\ServiceCategoryRepository;
use App\Repository\StoreServicesRepository;
use App\Repository\TypeServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ServiceSetting
{
    private $security;

    private $typeServiceRepository;

    private $manager;

    private$recommandationRepository;

    private $storeServicesRepository;

    private $communities;

    private ServiceCategoryRepository $serviceCategoryRepository;

    public function __construct(Security $security, TypeServiceRepository $typeServiceRepository, EntityManagerInterface $manager, RecommandationRepository $recommandationRepository, StoreServicesRepository $storeServicesRepository, Communities $communities, ServiceCategoryRepository $serviceCategoryRepository)
    {
        $this->security = $security;
        $this->typeServiceRepository = $typeServiceRepository;
        $this->manager = $manager;
        $this->recommandationRepository = $recommandationRepository;
        $this->storeServicesRepository = $storeServicesRepository;
        $this->communities = $communities;
        $this->serviceCategoryRepository = $serviceCategoryRepository;
    }

    public function setType(Service $service): Service
    {
        if($this->security->isGranted('ROLE_ADMIN_STORE') ){
            $type = $this->typeServiceRepository->findOneBy(['name' => 'store']);
            $service->setType($type);
        }elseif ($this->security->isGranted('ROLE_ADMIN_COMPANY')){
            $type = $this->typeServiceRepository->findOneBy(['name' => 'company']);
            $service->setType($type);
        }elseif ($this->security->isGranted('ROLE_USER')){
            if ($this->security->getUser()->getType()->getId() == 6){
                $type = $this->typeServiceRepository->findOneBy(['name' => 'company']);
                $service->setType($type);
            }elseif ($this->security->getUser()->getType()->getId() == 2){
                $type = $this->typeServiceRepository->findOneBy(['name' => 'store']);
                $service->setType($type);
            }
        }

        return $service;
    }

    public function setToParent(Service $service): Service
    {
        if($this->security->isGranted('ROLE_ADMIN_STORE') ){
            //avoid manyToMany with StoreService entity
            $storeService = $this->setStoreService($this->security->getUser()->getStore(), $service);

            $this->security->getUser()->getStore()->addService($storeService);
        }elseif ($this->security->isGranted('ROLE_ADMIN_COMPANY')){
            $this->security->getUser()->getCompany()->addService($service);
        }

        return $service;
    }

    public function setStoreService($store, $service)
    {
        $storeService = new StoreService();
        $storeService->setStore($store);
        $storeService->setService($service);

        $this->manager->persist($storeService);

        return $storeService;
    }

    public function getNbRecommandations($service, $nbRecommandations): array
    {
        if ($service->getType()){
            if ($service->getType()->getName() == 'company') {
                $nbRecommandation = count($this->recommandationRepository->findBy(['company' => $company = $service->getUser()->getCompany(), 'service' => $service, 'status'=>'Validated']));
                $nbRecommandations[$service->getId()] = $nbRecommandation;
            }elseif ($service->getType()->getName() == 'store'){
                $nbRecommandation = count($this->recommandationRepository->findBy(['store' => $store = $service->getUser()->getStore(), 'service' => $service, 'status'=>'Validated']));
                $nbRecommandations[$service->getId()] = $nbRecommandation;
            }elseif ($service->getType()->getName() == 'plateform' && $this->security->getUser()){
                //Get assocaition if exist
                $storeService = $this->storeServicesRepository->findOneBy(['service' => $service, 'store' => $this->security->getUser()->getStore()]);
                if ($storeService){
                    $nbRecommandation = count($this->recommandationRepository->findBy(['company' => null, 'service' => $service, 'status'=>'Validated']));
                    $nbRecommandations[$service->getId()] = $nbRecommandation;
                }
            }
        }

        return $nbRecommandations;
    }

    public function getDistance($service, $distances, Store $store = null): array
    {
        if ($service->getType()->getName() == 'company') $item = $service->getUser()->getCompany();
        elseif ($service->getType()->getName() == 'store') $item = $service->getUser()->getStore();
        elseif ($service->getType()->getName() == 'plateform'){
            //Get assocaition if exist
            $storeService = $this->storeServicesRepository->findOneBy(['service' => $service, 'store' => $this->security->getUser()->getStore()]);
            if ($storeService)  $item = $this->security->getUser()->getStore();
        }

        if ($this->security->getUser() && $this->security->getUser()->getCompany())
            $distance = $this->communities->calculateDistanceBetween($this->security->getUser()->getCompany(), $item ?? null, 'K');
        elseif ($store)
            $distance = $this->communities->calculateDistanceBetween($store, $item ?? null, 'K');
        else
            $distance = null;

        $distances[$service->getId()] = $distance;

        return $distances;
    }

    /**
     * Get distance between service and lat lon
     */
    public function getDistancesServicesWithLatLon($services, $lat, $lon)
    {
        $distances = [];
        $locations = [];
        foreach ($services as $service){
            if ($service->getType()->getName() == 'company') $item = $service->getUser()->getCompany();
            elseif ($service->getType()->getName() == 'store') $item = $service->getUser()->getStore();
            elseif ($service->getType()->getName() == 'plateform'){
                //Get assocaition if exist
                $storeService = $this->storeServicesRepository->findOneBy(['service' => $service, 'store' => $this->security->getUser()->getStore()]);
                if ($storeService)  $item = $this->security->getUser()->getStore();
            }

            $distance = $this->communities->calculateDistanceLonLat($item->getLatitude(), $item->getLongitude(), $lat, $lon, 'K');

            $distances[$service->getId()] = $distance;
            $locations[$service->getId()] = $item->getCity();
        }

        return $distances;
    }

    /**
     * Get distance between service and lat lon
     */
    public function getCityServices($services)
    {
        $locations = [];
        foreach ($services as $service){
            if ($service->getType()->getName() == 'company') $item = $service->getUser()->getCompany();
            elseif ($service->getType()->getName() == 'store') $item = $service->getUser()->getStore();
            elseif ($service->getType()->getName() == 'plateform'){
                //Get assocaition if exist
                $storeService = $this->storeServicesRepository->findOneBy(['service' => $service, 'store' => $this->security->getUser()->getStore()]);
                if ($storeService)  $item = $this->security->getUser()->getStore();
            }

            $locations[$service->getId()] = $item->getCity();
        }

        return $locations;
    }

    /**
     * Get services is labeled
     */
    public function isLabeledServices($services)
    {
        $labeled = [];
        foreach ($services as $service){
            if ($service->getType()->getName() == 'company'){
                $item = $service->getUser()->getCompany();
                $labeled[$service->getId()] = $item->getLabel();
            }

        }

        return $labeled;
    }

    public function getInfosServices($services, Store $store = null): array
    {
        $infos = [];
        $nbRecommandations = [];
        $distances = [];
        foreach ($services as $service){
            $nbRecommandations = $this->getNbRecommandations($service, $nbRecommandations);
            //Get nb Km between current sottore company and company item
            $distances = $this->getDistance($service, $distances, $store);
        }
        $infos['nbRecommandations'] = $nbRecommandations;
        $infos['distances'] = $distances;

        return $infos;
    }

    public function setGalleryFileName($service, $request){
        $imgGallery =  $request->request->get('service')['imgGallerie'];
        $imgGallery1 =  $request->request->get('service')['imgGallerie1'];
        $imgGallery2 =  $request->request->get('service')['imgGallerie2'];
        $imgGallery3 =  $request->request->get('service')['imgGallerie3'];
        if($imgGallery != 'edit') {
            $service->setFilename($imgGallery);
        }
        if($imgGallery1 != 'edit') {
            $service->setFilename1($imgGallery1);
        }
        if($imgGallery2 != 'edit') {
            $service->setFilename2($imgGallery2);
        }
        if($imgGallery3 != 'edit') {
            $service->setFilename3($imgGallery3);
        }
    }

    public function categoryExist(string $categoryString): bool
    {
        $result = $this->serviceCategoryRepository->findOneBy(['name'=> $categoryString]);

        if ($result)  return true;
        return false;
    }

    // Generate an array contains a key -> value with the errors where the key is the name of the form field
    public function floatvalue($val){
        $val = str_replace(",",".",$val);
        $val = preg_replace('/\.(?=.*\.)/', '', $val);
        return floatval($val);
    }

}