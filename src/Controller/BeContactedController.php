<?php

namespace App\Controller;

use App\Entity\BeContacted;
use App\Entity\Company;
use App\Form\BeContactedType;
use App\Repository\BeContactedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BeContactedController extends AbstractController
{
    /**
     * @Route("/be/contacted/list/{status}", name="be_contacted_list")
     */
    public function index(BeContactedRepository $beContactedRepository, $status): Response
    {
        if ($status == 'current')
            $beContactedList = $beContactedRepository->findBy(['company' => $this->getUser()->getCompany(), 'isArchived' => false, 'isWaiting' => false]);
        elseif ($status == 'isWaiting')
            $beContactedList = $beContactedRepository->findBy(['company' => $this->getUser()->getCompany(), 'isWaiting' => true]);
        elseif ($status == 'isArchived')
            $beContactedList = $beContactedRepository->findBy(['company' => $this->getUser()->getCompany(), 'isArchived' => true]);

        return $this->render('default/beContacted/index.html.twig', [
            'profile' => $this->getUser()->getProfile(),
            'beContactedList' => $beContactedList,
            'status' => $status
        ]);
    }

    /**
     * @Route("/be/contacted/by/{slug}", name="be_contacted_new")
     */
    public function form(Request $request, EntityManagerInterface $manager, Company $company, BeContactedRepository $beContactedRepository): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(['message' => 'You can access this only using Ajax!'], 400);
        }

        $beContacted = new BeContacted();

        $form = $this->createForm(BeContactedType::class, $beContacted);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()){
                if ($beContactedRepository->findBy(['company' => $company, 'email' => $beContacted->getEmail(), 'isArchived' => false])){
                    $this->addFlash('warning', 'Une demande envoyé le ' . $beContacted->getCreatedAt()->format('d/m/Y') . ' est toujours en cours. '. $company->getName() . ' vous contactera  très prochainement.');
                }else{
                    $beContacted->setCompany($company);
                    $manager->persist($beContacted);

                    $manager->flush();

                    $this->addFlash('success', $company->getName() . ' a été notifiée et reviendra vers vous dans les plus brefs délais');
                }

                return new JsonResponse(['message' => 'Formulaire valide', 'data' => ''], 200);

            }else{
                return new JsonResponse(['message' => 'Formulaire invalide', 'data' => $this->getErrorMessages($form)], 400);
            }
        }

        return $this->render('default/beContacted/create.html.twig', [
            'form' => $form->createView(),
            'company' => $company
        ]);
    }

    // Generate an array contains a key -> value with the errors where the key is the name of the form field
    protected function getErrorMessages(FormInterface $form)
    {
        $errors = [];

        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }

    /**
     * @Route("/be/contacted/archive/{id}", name="be_contacted_archive")
     */
    public function archive(BeContacted $beContacted, EntityManagerInterface  $manager): Response
    {
        if ($beContacted->getIsArchived() == false){
            $beContacted->setIsArchived(true);
            $beContacted->setIsWaiting(false);

            $manager->persist($beContacted);
            $manager->flush();
        }

        return new JsonResponse(['message' => 'is archived'],200);
    }

    /**
     * @Route("/be/contacted/waiting/{id}", name="be_contacted_waiting")
     */
    public function wainting(BeContacted $beContacted, EntityManagerInterface  $manager): Response
    {
        if ($beContacted->getIsWaiting() == false){
            $beContacted->setIsWaiting(true);
            $beContacted->setIsArchived(false);

            $manager->persist($beContacted);
            $manager->flush();
        }

        return new JsonResponse(['message' => 'is waiting'],200);
    }
}
