<?php

namespace Cocur\Watchman;

/**
 * Trigger
 */
class Trigger
{
    /** @var Watch */
    private $watch;

    /** @var string */
    private $name;

    /** @var array */
    private $data;

    /**
     * @param Watch  $watch Watch the trigger is attached to.
     * @param string $name  Name of the trigger.
     * @param array  $data  Data of the trigger.
     */
    public function __construct(Watch $watch, $name, array $data = array())
    {
        $this->watch = $watch;
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * @return Watch Watch the trigger is attached to.
     */
    public function getWatch()
    {
        return $this->watch;
    }

    /**
     * @return string Name of the trigger.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array Trigger data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Deletes the trigger.
     *
     * @return boolean `true` if the trigger has been deleted.
     */
    public function delete()
    {
        return $this->watch->deleteTrigger($this->name);
    }
}
