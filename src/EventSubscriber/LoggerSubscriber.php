<?php


namespace App\EventSubscriber;

use App\Events\LoggerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber extends AbstractSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            LoggerEvent::SERVICE_NEW    => 'onServiceNew',
            LoggerEvent::SERVICE_SHOW  => 'onServiceShow',
            LoggerEvent::USER_NEW  => 'onUserNew',
        ];
    }

    /**
     * @param LoggerEvent $event
     */
    public function onServiceNew(LoggerEvent $event)
    {
        $this->logEntity(LoggerEvent::SERVICE_NEW, [
            'service' => $event->getEntity()
        ]);
    }

    /**
     * @param LoggerEvent $event
     */
    public function onServiceShow(LoggerEvent $event)
    {
        $this->logEntity(LoggerEvent::SERVICE_SHOW, [
            'service' => $event->getEntity()
        ]);
    }

    /**
     * @param LoggerEvent $event
     */
    public function onUserNew(LoggerEvent $event)
    {
        $this->logEntity(LoggerEvent::USER_NEW, [
            'user' => $event->getEntity()
        ]);
    }
}