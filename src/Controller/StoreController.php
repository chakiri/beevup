<?php

namespace App\Controller;

use App\Entity\Store;
use App\Form\StoreType;
use App\Repository\CompanyRepository;
use App\Repository\RecommandationRepository;
use App\Repository\ServiceRepository;
use App\Repository\StoreServicesRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use App\Service\Error\Error;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Map;
use App\Service\ImageCropper;
use App\Service\GetCompanies;


class StoreController extends AbstractController
{
    /**
     * @Route("/store/{slug}", name="store_show")
     */
    public function show(Store $store, UserRepository $userRepository, CompanyRepository $companyRepository, RecommandationRepository $recommandationRepository, GetCompanies $getCompanies)
    {
        $allCompanies = $getCompanies->getAllCompanies( $this->getUser()->getStore());
        $localStores = $getCompanies->getLocalStores($store , $allCompanies);

        //Denie access
        if ($getCompanies->isStoreInLocalStores($store, $localStores) != true || $this->getUser()->getStore() != $store) return $this->render('bundles/TwigBundle/Exception/error403.html.twig');

        $users = $userRepository->findByStore($store);
        $companies = $companyRepository->findBy(['store' => $store, 'isCompleted' => true], ['id' => 'DESC'], 3);

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
    }

    /**
     * @Route("/store/{id}/edit", name="store_edit")
     * @Route("/store/new", name="store_new")
     */
    public function form(Request $request, ?Store $store, EntityManagerInterface $manager)
    {
        //Denie Access
        if(!in_array('ROLE_ADMIN_STORE', $this->getUser()->getRoles())) return $this->render('bundles/TwigBundle/Exception/error403.html.twig');

        if (!$store) {
            $store = new Store();
            $store->setReference('12323434');
        }
        $form = $this->createForm(StoreType::class, $store);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $store->setModifiedAt(new \DateTime());
            $adresse = $store->getAddressNumber() . ' ' . $store->getAddressStreet() . ' ' . $store->getAddressPostCode() . ' ' . $store->getCity() . ' ' . $store->getCountry();
            $map = new Map();
            $coordonnees = $map->geocode($adresse);

            if ($coordonnees != null) {
                $store->setLatitude($coordonnees[0]);
                $store->setLongitude($coordonnees[1]);
            }

            $manager->persist($store);
            $manager->flush();

            return $this->redirectToRoute('store_show', [
                'slug' => $store->getSlug()
            ]);
        }

        return $this->render('store/form.html.twig', [
            'store' => $store,
            'form' => $form->createView()
        ]);
    }

}
