<?php

namespace App\Controller\Admin\Label;

use App\Entity\Label;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Routing\Annotation\Route;

class LabelController extends EasyAdminController
{
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
            ->setParameter('storeId', $this->getUser()->getStore()->getId())
        ;

        return $result;
    }

    public function zoomAction()
    {
        $id = $this->request->query->get('id');
        $entity = $this->em->getRepository(Label::class)->find($id);

        return $this->render('admin/file/vich_uploader_image_zoom.html.twig', [
            'item' => $entity
        ]);
    }

    /**
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
     * @Route("/reject/kbis/{label}", name="reject_kbis")
     */
    public function rejectKbis(Label $label, EntityManagerInterface $manager)
    {
        $label->setKbisStatus(null);

        $manager->persist($label);

        $manager->flush();

        //Send mail to notify user

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'Label',
            'action' => 'list'
        ]);
    }

}