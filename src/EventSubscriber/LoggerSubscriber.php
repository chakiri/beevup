<?php

namespace App\EventSubscriber;

use App\Events\LoggerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber extends AbstractSubscriber implements EventSubscriberInterface
{
    /**
     * Default action for logs
     */
    const UNKNOWN_ACTION = 'unknown_action';

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            LoggerEvent::class    => 'onLogEntity',
        ];
    }


    /**
     * @param LoggerEvent $event
     */
    public function onLogEntity(LoggerEvent $event): void
    {
        $this->logEntity($event->getName(), [
            self::getClassName($event->getEntity()) => $event->getEntity()
        ]);
    }

    /**
     * @param string $action
     * @param array $entityFields
     */
    protected function logEntity($action = self::UNKNOWN_ACTION, array $entityFields): void
    {
        $this->dbLogger->info($action, [
            'current_user' => $this->security->getUser()->getEmail(),
            'entity' => $entityFields
        ]);
    }

    /**
     * @param $object
     * @return mixed|string
     */
    private static function getClassName($object): string
    {
        $name = explode('\\', get_class($object));
        return end($name);
    }
}