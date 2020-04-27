<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Service;
use App\Entity\Store;
use App\Entity\User;
use App\Form\ServiceSearchType;
use App\Form\ServiceType;
use App\Repository\RecommandationRepository;
use App\Repository\ServiceRepository;
use App\Repository\CompanyRepository;
use App\Repository\StoreRepository;
use App\Repository\TypeServiceRepository;
use App\Repository\UserRepository;
use App\Service\ServiceSetting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class ServiceController extends AbstractController
{

    /**
    * @Route("/service", name="service")
    * @Route("/service/generic", name="service_generic")
    * @Route("/service/company/{company}", name="service_company")
    * @Route("/service/store/{store}", name="service_store")
    * @Route("/service/user/{user}", name="service_user")
    */
    public function index($user = null, $company = null, $store = null, Request $request, ServiceRepository $serviceRepository, TypeServiceRepository $typeServiceRepository, StoreRepository $storeRepository, UserRepository $userRepository, CompanyRepository $companyRepository)
    {
        $services = $serviceRepository->findBy([], ['createdAt' =>'DESC']);

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
            $services = $store->getServices();
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

            $services = $serviceRepository->findSearch($query, $category);

            $user = null;
        }

        return $this->render('service/index.html.twig', [
            'services' => $services,
            'isPrivate' => isset($user),
            'searchForm' => $searchForm->createView()
        ]);
    }

    /**
     * @Route("/service/{service}/associate", name="service_associate")
     */
    public function associate(Service $service, EntityManagerInterface $manager, StoreRepository $storeRepository)
    {
        if ($service && $service->getType()->getName() == 'plateform'){

            $store = $storeRepository->findOneBy(['id' => $this->getUser()->getStore()]);

            $store->addService($service);

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
    public function dissociate(Service $service, EntityManagerInterface $manager, StoreRepository $storeRepository)
    {
        if ($service && $service->getType()->getName() == 'plateform'){

            $store = $storeRepository->findOneBy(['id' => $this->getUser()->getStore()]);

            $store->removeService($service);

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
     * @Route("/service/new", name="service_new")
     */
    public function form(?Service $service, Request $request, EntityManagerInterface $manager, ServiceSetting $serviceSetting)
    {
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
            if (!$service->getType()){
                //Set type depending on user role
                $serviceSetting->setType($service);
            }
            $serviceSetting->setToParent($service);
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
    public function show(Service $service, ServiceRepository $serviceRepository, RecommandationRepository $recommandationRepository, CompanyRepository $companyRepository)
    {
        $companyId = 0;
        $storeId = 0;
        $company = null;
        $store = null;

        if(!is_null($service->getUser()->getCompany())) {
            $company = $companyRepository->findOneById($service->getUser()->getCompany()->getId());
            $companyId = $company->getId();
        }

        $similarServices = $serviceRepository->findBy(['category' => $service->getCategory()], [], 3);

        $recommandations = $recommandationRepository->findBy(['service' => $service, 'status'=>'Validated']);

        $recommandationsCompany = $recommandationRepository->findBy(['company' => $company, 'service' => null, 'status'=>'Validated']);

        return $this->render('service/show.html.twig', [
            'service' => $service,
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
}
