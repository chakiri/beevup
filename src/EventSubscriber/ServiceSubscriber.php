<?php


namespace App\EventSubscriber;

use App\Events\ServiceEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ServiceSubscriber extends AbstractSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ServiceEvent::SERVICE_NEW    => 'onServiceNew',
            ServiceEvent::SERVICE_SHOW  => 'onServiceShow',
        ];
    }

    /**
     * @param ServiceEvent $event
     */
    public function onServiceNew(ServiceEvent $event)
    {
        $this->logEntity(ServiceEvent::SERVICE_NEW, [
            'service' => $event->getEntity()
        ]);
    }

    /**
     * @param ServiceEvent $event
     */
    public function onServiceShow(ServiceEvent $event)
    {
        $this->logEntity(ServiceEvent::SERVICE_SHOW, [
            'service' => $event->getEntity()
        ]);
    }
}