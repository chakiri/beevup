<?php


namespace App\Twig;


use App\Entity\Label;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig Extension used in templates/bundles/EasyAdminBundle/default/list.html.twig
 * Filter used in list.html.twig line 58
 */
class EasyAdminExtension extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('filter_admin_actions', [$this, 'filterActions'])
        ];
    }

    /**
     * Active actions step by step
     */
    public function filterActions(array $itemActions, $item)
    {
        if ($item instanceof Label){
            if ($item->getKbisStatus() !== 'isValid'){
                unset($itemActions['edit']);
            }
            if (!$item->getStoreAppointment() || ($item->getStoreAppointment() && $item->getStoreAppointment() > (new \DateTime()))){
                unset($itemActions['validateLabel']);
            }
        }

        return $itemActions;
    }
}