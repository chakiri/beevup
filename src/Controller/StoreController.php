<?php

namespace App\Controller;

use App\Entity\Store;
use App\Form\StoreType;
use App\Repository\CompanyRepository;
use App\Repository\ServiceRepository;
use App\Repository\StoreRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StoreController extends AbstractController
{
    /**
     * @Route("/store", name="store")
     */
    public function index()
    {
        return $this->render('store/show.html.twig', [
            'controller_name' => 'StoreController',
        ]);
    }

    /**
     * @Route("/store/{slug}", name="store_show")
     */
    public function show(Store $store, UserRepository $userRepository, CompanyRepository $companyRepository, StoreRepository $storeRepository, UserTypeRepository $userTypeRepository, ServiceRepository $serviceRepository)
    {
        $users =  $userRepository->findByStore($store);
        $companies = $companyRepository->findBy(['store'=> $store, 'isCompleted'=>true]);
        $usersType = $userTypeRepository->findBy(['id'=>array(1,2,4)]);
        $storeUsers =$userRepository->findBy(['store'=> $store, 'type'=> $usersType]);

        $services = $serviceRepository->findBy(['user'=>$storeUsers]);


        return $this->render('store/show.html.twig', [
            'store' => $store,
            'users' => $users,
            'companies' =>$companies,
            'services' =>$services
        ]);
    }

    /**
     * @Route("/store/{id}/edit", name="store_edit")
     * @Route("/store/new", name="store_new")
     */
    public function form(Request $request, ?Store $store, EntityManagerInterface $manager)
    {
        if (!$store){
            $store = new Store();
            $store->setReference('12323434');
        }
        $form = $this->createForm(StoreType::class, $store);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $store->setModifiedAt( new \DateTime());

            $file = $form['imageFile']->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename =  $originalFilename;
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('stores_images'),
                        $newFilename
                    );

                } catch (FileException $e) {}
                $store->setFilename($newFilename);
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
