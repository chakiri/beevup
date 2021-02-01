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
     * @var string
     */
    protected $name;

    /**
     * AbstractEvent constructor.
     * @param null $entity
     */
    public function __construct($entity = null, $name = null)
    {
        $this->entity = $entity;
        $this->name = $name;
    }

    /**
     * @return bool|null
     */
    public function getEntity()
    {
        if($this->entity != null) {
            return $this->entity;
        }
        return false;
    }

    /**
     * @return bool|string|null
     */
    public function getName()
    {
        if($this->name != null) {
            return $this->name;
        }
        return false;
    }
}