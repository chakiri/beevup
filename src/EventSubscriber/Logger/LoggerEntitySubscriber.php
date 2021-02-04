<?php

namespace App\EventSubscriber\Logger;

use App\Event\Logger\LoggerEntityEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerEntitySubscriber extends AbstractSubscriber implements EventSubscriberInterface
{
    /**
     * Default action for logs
     */
    const UNKNOWN_ACTION = 'unknown_action';

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LoggerEntityEvent::class    => 'onLogEntity',
        ];
    }

    /**
     * @param LoggerEntityEvent $event
     */
    public function onLogEntity(LoggerEntityEvent $event): void
    {
        $this->logEntity($event->getNameAction(), [
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
     * Get name object from namespace
     * @param $object
     * @return mixed|string
     */
    private static function getClassName($object): string
    {
        $name = explode('\\', get_class($object));
        return end($name);
    }
}