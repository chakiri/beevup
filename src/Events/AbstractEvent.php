<?php

namespace App\Events;

use Symfony\Contracts\EventDispatcher\Event;

class AbstractEvent extends Event
{
    /**
     * @var null
     */
    protected $entity;

    /**
     * AbstractEvent constructor.
     * @param null $entity
     */
    public function __construct($entity = null)
    {
        $this->entity = $entity;
    }

    /**
     * @return object|bool
     */
    public function getEntity(): ?object
    {
        if($this->entity != null) {
            return $this->entity;
        }
        return false;
    }
}