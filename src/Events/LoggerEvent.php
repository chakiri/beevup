<?php

namespace App\Events;

class LoggerEvent extends AbstractEvent
{
    const USER_NEW     = 'user_new';
    const USER_LOGIN   = 'user_login';
    const SERVICE_NEW  = 'service_new';
    const SERVICE_SHOW = 'service_show';
    const COMPANY_SHOW = 'company_show';

    /**
     * @var string
     */
    protected $nameAction;

    public function __construct($entity = null, $nameAction = null)
    {
        parent::__construct($entity);
        $this->nameAction = $nameAction;
    }

    /**
     * @return string|bool
     */
    public function getName()
    {
        return $this->nameAction ?? false;
    }
}