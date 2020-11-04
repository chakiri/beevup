<?php

namespace App\Controller;

use App\Entity\Store;
use App\Form\StoreType;
use App\Repository\CompanyRepository;
use App\Repository\RecommandationRepository;
use App\Repository\ServiceRepository;
use App\Repository\StoreRepository;
use App\Repository\StoreServicesRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use App\Service\Error\Error;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Intl\Intl;
use App\Service\Map;
use App\Service\ImageCropper;
use App\Service\GetCompanies;


class StoreController extends AbstractController
{

    /**
     * @Route("/store/{slug}", name="store_show")
     */
    public function show(Store $store, UserRepository $userRepository, CompanyRepository $companyRepository, RecommandationRepository $recommandationRepository, UserTypeRepository $userTypeRepository, ServiceRepository $serviceRepository, StoreServicesRepository $storeServicesRepository, GetCompanies $getCompanies)
    {
        $allCompanies = $getCompanies->getAllCompanies( $this->getUser()->getStore());
        $localStores = $getCompanies->getLocalStores($store , $allCompanies);
        if ($getCompanies->isStoreInLocalStores($store, $localStores) == true || $this->getUser()->getStore() == $store ) {
            $users = $userRepository->findByStore($store);
            $companies = $companyRepository->findBy(['store' => $store, 'isCompleted' => true], ['id' => 'DESC'], 3);
            //$usersType = $userTypeRepository->findBy(['id' => array(1, 2, 4)]);
            //$storeUsers = $userRepository->findBy(['store' => $store, 'type' => $usersType]);
            //$services = $serviceRepository->findBy(['user' => $storeUsers]);
            $services = [];
            foreach ($store->getServices() as $service){
                array_push($services, $service->getService());
            }

            $recommandationsServices = $recommandationRepository->findByStoreServices($store, 'Validated');
            $recommandationsStore = $recommandationRepository->findByStoreWithoutServices($store, 'Validated');

            return $this->render('store/show.html.twig', [
                'store' => $store,
                'users' => $users,
                'companies' => $companies,
                'services' => $services,
                'recommandationsServices' => $recommandationsServices,
                'recommandationsStore' => $recommandationsStore
            ]);
        }else{
            return $this->redirectToRoute('page_not_found', []);
        }
    }

    /**
     * @Route("/store/{id}/edit", name="store_edit")
     * @Route("/store/new", name="store_new")
     */
    public function form(Request $request, ?Store $store, EntityManagerInterface $manager, $id,  ImageCropper $imageCropper, Error $error)
    {
       if(in_array('ROLE_ADMIN_STORE', $this->getUser()->getRoles())) {
            if (!$store) {
                $store = new Store();
                $store->setReference('12323434');
            }
            $form = $this->createForm(StoreType::class, $store);
            $form->handleRequest($request);

            if ($form->isSubmitted() ) {
                if($form->isValid())
                {
                    $store->setModifiedAt(new \DateTime());
                    $adresse = $store->getAddressNumber() . ' ' . $store->getAddressStreet() . ' ' . $store->getAddressPostCode() . ' ' . $store->getCity() . ' ' . $store->getCountry();
                    $map = new Map();
                    $coordonnees = $map->geocode($adresse);

                    if ($coordonnees != null) {
                        $store->setLatitude($coordonnees[0]);
                        $store->setLongitude($coordonnees[1]);
                    }

                    //Cropped Image
                    $imageCropper->move_directory($store);

                    $manager->persist($store);
                    $manager->flush();

                    return $this->redirectToRoute('store_show', [
                        'slug' => $store->getSlug()
                    ]);
                }
                else{
                    return new JsonResponse( array(
                        'result' => 0,
                        'message' => 'Invalid form',
                        'data' => $error->getErrorMessages($form)
                    ));
                }
            }

            return $this->render('store/form.html.twig', [
                'store' => $store,
                'form' => $form->createView()
            ]);
        }
        else{
            return $this->redirectToRoute('page_not_found', []);
        }
    }

}
