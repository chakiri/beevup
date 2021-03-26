<?php

namespace App\Controller;

use App\Entity\Service;
use App\Event\Logger\LoggerSearchEvent;
use App\Event\Logger\LoggerEntityEvent;
use App\Form\ServiceSearchType;
use App\Form\ServiceType;
use App\Repository\CategoryRepository;
use App\Repository\PostCategoryRepository;
use App\Repository\PostRepository;
use App\Repository\RecommandationRepository;
use App\Repository\ScorePointRepository;
use App\Repository\ServiceCategoryRepository;
use App\Repository\ServiceRepository;
use App\Repository\CompanyRepository;
use App\Repository\StoreRepository;
use App\Repository\StoreServicesRepository;
use App\Repository\TypeServiceRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use App\Service\Error\Error;
use App\Service\Factory\ServiceFactory;
use App\Service\ScoreHandler;
use App\Service\ServiceSetting;
use App\Service\GetCompanies;
use App\Service\AutomaticPost;
use App\Service\ImageCropper;
use App\Service\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

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
    public function index($user = null, $company = null, $store = null, Request $request, ServiceRepository $serviceRepository, TypeServiceRepository $typeServiceRepository, StoreRepository $storeRepository, UserRepository $userRepository, CompanyRepository $companyRepository, GetCompanies $getCompanies, ServiceSetting $serviceSetting, EventDispatcherInterface $dispatcher, Utility $utility)
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
            $services = $serviceRepository->findBy(['type' => $typeService], ['createdAt' => 'DESC']);
        }
        if ($request->get('_route') == 'service_discovery') {
            $services = $serviceRepository->findByIsDiscovery($allCompanies, $this->getUser()->getStore());
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

            //Add services of store if match query
            $storeServices = $serviceRepository->findSearchStoreServices($storeServices, $query, $category, $isDiscovery);
            $services = array_merge($services, $storeServices);

            //Dispatch on Logger Search Event
            $fields = [
                'query' => [
                    'query_name' => $query,
                    'query_category' => $category,
                    'query_discovery' => $isDiscovery
                ],
                'nb_result' => count($services),
                'ids_result' => $utility->getIdsOfArray($services)
            ];
            $dispatcher->dispatch(new LoggerSearchEvent(LoggerSearchEvent::SERVICE_SEARCH, $fields));

            $user = null;
        }

        //Get advisor of store
        $adviser= $userRepository->findOneBy(['id'=>$this->getUser()->getStore()->getDefaultAdviser()]);

        //Get informations of services
        $infos = $serviceSetting->getInfosServices($services);

        return $this->render('service/index.html.twig', [
            'services' => $services,
            'distances' => $infos['distances'],
            'nbRecommandations' => $infos['nbRecommandations'],
            'isPrivate' => isset($user),
            'isDiscovery' => $request->get('_route') == 'service_discovery',
            'adviser'=> $adviser,
            'searchForm' => $searchForm->createView()
        ]);
    }

    /**
     * @Route("/service/model", name="service_model")
     */
    public function model(Request $request, ServiceRepository $serviceRepository, TypeServiceRepository $typeServiceRepository)
    {
        $typeService = $typeServiceRepository->findOneBy(['name' => 'model']);
        $services = $serviceRepository->findBy(['type' => $typeService], ['createdAt' => 'DESC']);
        $template = 'service/model.html.twig';

        //If searching
        if (null !== $query = $request->get('query')){
            $services = $serviceRepository->findModel($typeService, $query);
            $template = 'service/modelResult.html.twig';
        }

        return $this->render($template, [
            'services' => $services
        ]);
    }

    /**
     * @Route("/service/{id}/model", name="service_from_model")
     */
    public function fromModel(Service $service, EntityManagerInterface $manager, TypeServiceRepository $typeServiceRepository, EventDispatcherInterface $dispatcher)
    {
        $type =  $typeServiceRepository->findOneBy(['name' => 'company']);
        $newService = ServiceFactory::create($service, $this->getUser(), $type);

        //Dispatch on Logger Entity Event
        $dispatcher->dispatch(new LoggerEntityEvent(LoggerEntityEvent::SERVICE_NEW_MODEL, $service));

        $manager->persist($newService);

        $manager->flush();

        return $this->redirectToRoute('service_edit', [
            'id' => $newService->getId()
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
     public function form(EventDispatcherInterface $dispatcher, ?Service $service, $isOffer = false, Request $request, EntityManagerInterface $manager, ServiceSetting $serviceSetting, ScoreHandler $scoreHandler, PostCategoryRepository $postCategoryRepository, AutomaticPost $autmaticPost, PostRepository $postRepository, ImageCropper $imageCropper, Error $error, ScorePointRepository $scorePointRepository)
    {
        if ($service != null && $request->get('_route') == 'service_edit' && $service->getUser()->getId() != $this->getUser()->getId())  return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        $referer = $request->headers->get('referer');
        $previousPage =  strpos($referer, 'company')== true ? 'company' : 'other';



        $message = 'Votre Service a bien été mis à jour !';
        if (!$service){
            $service = new Service();
            $url = $this->generateUrl('service_new');
            $message = "Votre Service a bien été crée ! <a href='$url'>Créer un nouveau service</a>";
            $service->setUser($this->getUser());
        }

        $form = $this->createForm(ServiceType::class, $service, array('isOffer'=>$isOffer));
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            if($form->isValid())
            {
                /* ======== get selected image from gallery ========*/
                $serviceSetting->setGalleryFileName($service,$request);

                //Set type depending on user role
                if (!$service->getType())
                    $serviceSetting->setType($service);

                $serviceSetting->setToParent($service);

                //Add score to user if creation
                $optionsRedirect = [];
                if ($service->getIsDiscovery()) {
                    $nbPoints = $scorePointRepository->findOneBy(['id' => 3])->getPoint();
                    if (!$service->getId()) {
                        $scoreHandler->add($this->getUser(), $nbPoints);
                        $optionsRedirect = ['toastScore' => $nbPoints];
                    }
                }
                $service->setPrice($this->floatvalue($service->getPrice()));

                //Cropped image
                $imageCropper->move_directory($service);
                $manager->persist($service);

                /***** if the user change the service it should be updated in the posts ********/
                $relatedPost = $postRepository->findPostRelatedToService($service);
                if ($relatedPost != null) {
                    $relatedPost->setTitle($autmaticPost->generateTitle($service));
                    $manager->persist($relatedPost);
                }
                /** ******************** **/

                $manager->flush();

                /**Add automatic post
                 * when the  user create a new service an automatic post will be created
                 ***/
                if ($request->get('_route') == 'service_new') {
                    //Dispatch on Logger Entity Event
                    $dispatcher->dispatch(new LoggerEntityEvent(LoggerEntityEvent::SERVICE_NEW, $service));

                    $category = $postCategoryRepository->findOneBy(['id' => 8]);
                    $autmaticPost->Add($this->getUser(), $autmaticPost->generateTitle($service), '', $category, $service->getId(), 'Service');
                }

                //Merge score option to options array
                $optionsRedirect = array_merge($optionsRedirect, ['id' => $service->getId()]);

                $this->addFlash('success', $message);

                if ($request->isXmlHttpRequest())
                {
                   return new JsonResponse( array(
                        'message'=> $service->getId()
                    ));
               }
                else {
                    return $this->redirectToRoute('service_show', $optionsRedirect);
                }
            }
            else{
                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'result' => 0,
                        'message' => 'Invalid form',
                        'data' => $error->getErrorMessages($form),
                    ));
                }
            }
        }

        return $this->render('service/form.html.twig', [
            'service' => $service,
            'ServiceForm' => $form->createView(),
            'edit' => $service->getId() != null,
            'previous'=>$previousPage
        ]);
    }

    /**
    * @Route("/service/{id}", name="service_show")
    */
    public function show(EventDispatcherInterface $dispatcher, Service $service, ServiceRepository $serviceRepository, RecommandationRepository $recommandationRepository, StoreServicesRepository $storeServicesRepository, UserRepository $userRepository, UserTypeRepository $userTypeRepository, GetCompanies $getCompanies, $id )
    {
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
        $recommandationsCompany = $recommandationRepository->findBy(['company' => $service->getUser()->getCompany(), 'service' => null, 'status'=>'Validated']);

        //Dispatch on Logger Entity Event
        if ($service->getUser() != $this->getUser())
            $dispatcher->dispatch(new LoggerEntityEvent(LoggerEntityEvent::SERVICE_SHOW, $service));

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
    /**
     * @Route("/service/{id}/delete/{fileId}", name="delete-file")
     */
    public function deleteFile(Service $service, EntityManagerInterface $manager, $fileId){


        if($fileId == 'service_imageFile1') {
            $service->setFilename1(null);
        } elseif($fileId == 'service_imageFile2'){
            $service->setFilename2(null);
        } elseif($fileId == 'service_imageFile3'){
            $service->setFilename3(null);
        }
           $manager->persist($service);
           $manager->flush();
            return new JsonResponse( array(
                'result' => 0,
            ));
    }

    // Generate an array contains a key -> value with the errors where the key is the name of the form field
    private function floatvalue($val){
        $val = str_replace(",",".",$val);
        $val = preg_replace('/\.(?=.*\.)/', '', $val);
        return floatval($val);
    }

    /**
     * @Route("/service/category/list", name="service_category_list", methods="GET", options={"expose"=true})
     */
    public function getCategoriesApi(Request $request, ServiceCategoryRepository $serviceCategoryRepository)
    {
        $query = $request->get('query');

        $categories = $serviceCategoryRepository->findAllMatching($query, 5);

        return $this->json([
            'categories' => $categories
        ], 200);
    }

}
