<?php

namespace App\EventSubscriber\Logger;

use App\Event\Logger\LoggerSearchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSearchSubscriber extends AbstractSubscriber implements EventSubscriberInterface
{

    const UNKNOWN_ACTION = 'unknown_action';

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LoggerSearchEvent::class => 'onLogSearch',
        ];
    }

    /**
     * @param LoggerSearchEvent $event
     */
    public function onLogSearch(LoggerSearchEvent $event): void
    {
        $this->logSearch($event->getNameAction(), $event->getFields());
    }

    /**
     * @param string $action
     * @param array $fields
     */
    protected function logSearch($action = self::UNKNOWN_ACTION, array $fields): void
    {
        $this->dbLogger->info($action, [
            'current_user_email' => $this->security->getUser()->getEmail(),
            'current_user_id' => $this->security->getUser()->getId(),
            'search' => [
                'query' => $fields['query'],
                'nb_result' => $fields['nb_result'],
                'ids_result' => $fields['ids_result']
            ]
        ]);
    }

}