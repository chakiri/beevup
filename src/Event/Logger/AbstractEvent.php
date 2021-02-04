<?php

namespace App\Event\Logger;

use Symfony\Contracts\EventDispatcher\Event;

class AbstractEvent extends Event
{
    protected $nameAction;

    /**
     * AbstractEvent constructor.
     * @param null $nameAction
     */
    public function __construct($nameAction = null)
    {
        $this->nameAction = $nameAction;
    }

    /**
     * @return string|bool
     */
    public function getNameAction()
    {
        return $this->nameAction ?? false;
    }
}