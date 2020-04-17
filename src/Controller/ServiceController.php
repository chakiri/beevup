<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\RecommandationRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\ORM\EntityManagerInterface;

class ServiceController extends AbstractController
{

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
     * @Route("/service/{id}/edit", name="service_edit")
     * @Route("/service/new", name="service_new")
     */

    public function form(?Service $service, Request $request, EntityManagerInterface $manager)
    {
        if (!$service){
            $service = new Service();
        }
        $service->setUser($this->getUser());
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

                } catch (FileException $e) { }
                $service->setPhoto($newFilename);
            }
            $manager->persist($service);
            $manager->flush();
            $this->addFlash('success', 'Votre Service a été mis à jour !');

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

    public function show(Service $service, RecommandationRepository $recommandationRepository, UserRepository $userRepository, CompanyRepository $companyRepository){
        $recommandations = $recommandationRepository->findBy(['service' => $service->getId(), 'status'=>'Validated'], []);
        $company = $companyRepository->findOneById($service->getUser()->getCompany()->getId());
        return $this->render('service/show.html.twig', [
            'service' => $service,
            'companyId'  => $company->getId(),
            'recommandations'=> $recommandations
        ]);
    }
}
