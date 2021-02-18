<?php

namespace App\Event\Logger;

class LoggerEntityEvent extends AbstractEvent
{
    const USER_NEW          = 'user_new';
    const USER_LOGIN        = 'user_login';
    const SERVICE_NEW       = 'service_new';
    const SERVICE_SHOW      = 'service_show';
    const COMPANY_SHOW      = 'company_show';
    const SERVICE_NEW_MODEL = 'service_new_model';

    private $entity;

    public function __construct($nameAction = null, $entity = null)
    {
        parent::__construct($nameAction);
        $this->entity = $entity;
    }

    /**
     * @return object|bool
     */
    public function getEntity(): ?object
    {
        if(null !== $this->entity) {
            return $this->entity;
        }
        return false;
    }
}