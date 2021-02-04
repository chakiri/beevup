<?php

namespace App\Event\Logger;

class LoggerSearchEvent extends AbstractEvent
{
    const SERVICE_SEARCH = 'service_search';

    private $fields;

    public function __construct($nameAction = null, array $fields = null)
    {
        parent::__construct($nameAction);
        $this->fields = $fields;
    }

    /**
     * @return array|bool
     */
    public function getFields()
    {
        if(null !== $this->fields) {
            return $this->fields;
        }
        return false;
    }
}