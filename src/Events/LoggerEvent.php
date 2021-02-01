<?php


namespace App\Events;


class LoggerEvent extends AbstractEvent
{
    const LOG_ENTITY   = 'log_entity';

    const USER_NEW     = 'user_new';
    const USER_LOGIN   = 'user_login';
    const SERVICE_NEW  = 'service_new';
    const SERVICE_SHOW = 'service_show';
    const COMPANY_SHOW = 'company_show';
}