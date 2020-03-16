<?php

namespace App\Controller;

use App\Entity\Store;
use App\Form\StoreType;
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
    public function show(Store $store)
    {
        return $this->render('store/show.html.twig', [
            'store' => $store
        ]);
    }

    /**
     * @Route("/store/{id}/edit", name="store_edit")
     */
    public function form(Request $request, Store $store, EntityManagerInterface $manager)
    {
        $form = $this->createForm(StoreType::class, $store);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($store);

            $manager->flush();

            return $this->redirectToRoute('store_show', [
                'slug' => $store->getSlug()
            ]);
        }

        return $this->render('store/edit.html.twig', [
            'store' => $store,
            'form' => $form->createView()
        ]);
    }
}
