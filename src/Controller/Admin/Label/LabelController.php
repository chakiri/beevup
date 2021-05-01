<?php

namespace App\Controller\Admin\Label;

use App\Entity\Label;
use App\Service\Chat\AutomaticMessage;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Routing\Annotation\Route;

class LabelController extends EasyAdminController
{
    /**
     * Get result to display in list
     */
    protected function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null)
    {
        //Get query builder from controller esay admin
        $result = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        //Custom result of query builder
        $result
            ->leftJoin('entity.company', 'c')
            ->leftJoin('c.store', 's')
            ->andWhere('s.id = :storeId')
            ->andWhere('entity.charter = true')
            ->andWhere('entity.kbisStatus is not null')
            ->andWhere('entity.isLabeled = false')
            ->orderBy('entity.createdAt', 'DESC')
            ->setParameter('storeId', $this->getUser()->getStore()->getId())
        ;

        return $result;
    }

    /**
     * Route to display kbis in modal
     * @Route("/modal/kbis/{label}", name="modal_kbis", options={"expose"=true})
     */
    public function modalKbis(Label $label)
    {
        return $this->render('admin/file/kbis_preview.html.twig', [
            'item' => $label
        ]);
    }

    /**
     * Action to validate label
     */
    public function validateLabelAction()
    {
        $id = $this->request->query->get('id');
        $entity = $this->em->getRepository(Label::class)->find($id);

        $entity->setIsLabeled(true);

        $this->em->persist($entity);

        $this->em->flush();

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'Label',
            'action' => 'list'
        ]);
    }

    /**
     * Action to validate kbis
     *
     * @Route("/valid/kbis/{label}", name="valid_kbis")
     */
    public function validateKbis(Label $label, EntityManagerInterface $manager)
    {
        $label->setKbisStatus('isValid');

        $manager->persist($label);

        $manager->flush();

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'Label',
            'action' => 'list'
        ]);
    }

    /**
     * Action to rejetc kbis
     *
     * @Route("/reject/kbis/{label}", name="reject_kbis")
     */
    public function rejectKbis(Label $label, EntityManagerInterface $manager, AutomaticMessage $automaticMessage)
    {
        $label->setKbisStatus(null);

        $manager->persist($label);

        $manager->flush();

        //Send message to user
        $automaticMessage->fromAdvisorToUser($label->getCompany()->getCompanyAdministrator(), "Votre document Kbis n'a pas été accepté. Veuillez charger un document valide. ");

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'Label',
            'action' => 'list'
        ]);
    }

}