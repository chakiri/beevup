<?php

namespace App\Controller\Admin\Label;

use App\Entity\Label;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

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

}