<?php

namespace App\Controller;

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
use App\Service\ServiceSetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class ServiceController extends AbstractController
{

    /**
    * @Route("/service", name="service")
    * @Route("/service/user/{id}", name="service_user")
    * @Route("/service/generic", name="service_generic")
    */
    public function index(?User $user, Request $request, ServiceRepository $repository, TypeServiceRepository $typeServiceRepository, StoreRepository $storeRepository)
    {
        if ($user) {
            $servicesUser = $repository->findBy(['user' => $user], ['createdAt' => 'DESC']);
            $store = $storeRepository->findOneBy(['id' => $this->getUser()->getStore()]);
            $servicesAssociated = $store->getServices();
            $services = array_merge($servicesAssociated->toArray(), $servicesUser);
        }elseif ($request->get('_route') == 'service_generic') {
            $typeService = $typeServiceRepository->findOneBy(['name' => 'plateform']);
            $services = $repository->findBy(['type' => $typeService]);
        }else $services = $repository->findBy([], ['createdAt' =>'DESC']);

        $searchForm = $this->createForm(ServiceSearchType::class);

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted()){
            $query = $searchForm->get('query')->getData();
            $category = $searchForm->get('category')->getData();

            $services = $repository->findSearch($query, $category);

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

            $this->addFlash('success', 'Ce service a bien été ajouté à vos proposition !');

            return $this->redirectToRoute('service_user', [
                'id' => $this->getUser()->getId()
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

            return $this->redirectToRoute('service_user', [
                'id' => $this->getUser()->getId()
            ]);
        }
    }

    /**
     * @Route("/service/{id}/edit", name="service_edit")
     * @Route("/service/new", name="service_new")
     */
    public function form(?Service $service, Request $request, EntityManagerInterface $manager, ServiceSetType $serviceSetType)
    {
        $message = 'Votre Service a bien été mis à jour !';
        if (!$service){
            $service = new Service();
            $message = "Votre Service a bien été crée !";
            $service->setUser($this->getUser());
        }
        $service->setUser($this->getUser());
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if (!$service->getType()){
                //Set type depending on user role
                $serviceSetType->set($service);
            }
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
        $company = $companyRepository->findOneById($service->getUser()->getCompany()->getId());

        $similarServices = $serviceRepository->findBy(['category' => $service->getCategory()], [], 3);

        $recommandations = $recommandationRepository->findBy(['service' => $service, 'status'=>'Validated']);

        $recommandationsCompany = $recommandationRepository->findBy(['company' => $company, 'service' => null, 'status'=>'Validated']);

        return $this->render('service/show.html.twig', [
            'service' => $service,
            'companyId'  => $company->getId(),
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
