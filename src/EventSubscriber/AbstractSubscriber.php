<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

abstract class AbstractSubscriber
{
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

}