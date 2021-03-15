<?php

namespace App\Controller\Admin\Store;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class StoreController extends EasyAdminController
{

    protected function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null)
    {
        $store = $this->getUser()->getStore();
        if($this->getUser()->getType()->getId() !=5 )
        {
            $dqlFilter = sprintf('entity.id = %s', $store->getId());
        }

        $list = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);
        return $list;
    }
}