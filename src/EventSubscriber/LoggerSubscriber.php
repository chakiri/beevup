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
            LoggerEvent::LOG_ENTITY    => 'onLogEntity',
        ];
    }

    /**
     * @param LoggerEvent $event
     */
    public function onLogEntity(LoggerEvent $event)
    {
        $this->logEntity($event->getName(), [
            self::getClassName($event->getEntity()) => $event->getEntity()
        ]);
    }

    /**
     * @param $object
     * @return mixed|string
     */
    private static function getClassName($object) {
        $name = explode('\\', get_class($object));
        return end($name);
    }
}