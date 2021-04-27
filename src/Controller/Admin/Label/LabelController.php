<?php

namespace App\Controller\Admin\Label;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class LabelController extends EasyAdminController
{
    protected function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null)
    {
        $store = $this->getUser()->getStore();
        if($this->getUser()->getType()->getId() !=5 )
        {
            $dqlFilter = sprintf('entity.company.store.id = %s', $store->getId());
        }

        $list = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);
        return $list;
    }
}