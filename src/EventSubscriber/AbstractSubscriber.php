<?php


namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

abstract class AbstractSubscriber
{
    /**
     * Default action for logs
     */
    const UNKNOWN_ACTION = 'unknown_action';

    /**
     * @var LoggerInterface
     */
    protected $dbLogger;

    /**
     * @var Security
     */
    protected $security;

    /**
     * AbstractSubscriber constructor.
     * @param LoggerInterface $dbLogger
     * @param Security $security
     */
    public function __construct(LoggerInterface $dbLogger, Security $security)
    {
        $this->dbLogger = $dbLogger;
        $this->security = $security;
    }

    /**
     * @param string $action
     * @param array $entityFields
     */
    protected function logEntity($action = self::UNKNOWN_ACTION, array $entityFields)
    {
        $this->dbLogger->info($action, [
            'user' => $this->security->getUser()->getEmail(),
            'entity' => $entityFields
        ]);
    }
}