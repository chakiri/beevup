<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\ORM\EntityManagerInterface;

class ServiceController extends AbstractController
{
    /**
     * @Route("/service/create", name="service_create")
     */
    public function create(Request $request, EntityManagerInterface $manager){
        $service = new Service();

        $form = $this->createForm(ServiceType::class, $service);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['imageFile']->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename =  $originalFilename;
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('service_photo'),
                        $newFilename
                    );
                    
                } catch (FileException $e) {
                 
                   
                }
                $service->setPhoto($newFilename);
               
           }
           $service->setUser($this->getUser());
           $manager->persist($service);
           $manager->flush();
           $this->addFlash('create-service-success', 'Votre Service a été bien crée !');

           return $this->redirectToRoute('service_show', [
               'id' => $service->getId()
           ]);
        }
        return $this->render('service/form.html.twig', [
            'ServiceForm' => $form->createView(),
            'edit' => 0
        ]);
    }
    /**
     * @Route("/service", name="service")
     */
    public function index(ServiceRepository $repository)
    {
        $services = $repository->findAll();
        return $this->render('service/index.html.twig', [
            'services' => $services
        ]);
    }


    /**
     * @Route("/service/{id}", name="service_show")
     */
    public function show(Service $service){
        return $this->render('service/show.html.twig', [
            'service' => $service
        ]);
    }
        /**
     * @Route("/service/{id}/edit", name="service_edit")
     */
    public function edit(Request $request, Service $service, EntityManagerInterface $manager){
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form['imageFile']->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename =  $originalFilename;
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('service_photo'),
                        $newFilename
                    );
                    
                } catch (FileException $e) {
                 
                   
                }
                $service->setPhoto($newFilename);
               
               
           }
           $manager->persist($service);
           $manager->flush();
           $this->addFlash('update-service-success', 'Votre Service a été mis à jour !');

            return $this->redirectToRoute('service_show', [
                'id' => $service->getId()
            ]);
        }
        return $this->render('service/form.html.twig', [
            'service' => $service,
            'ServiceForm' => $form->createView(),
            'edit' => 1
        ]);
    }



}
