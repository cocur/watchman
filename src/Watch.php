<?php

namespace Cocur\Watchman;

/**
 * Watch
 */
class Watch
{
    /** @var Watchman */
    private $watchman;

    /** @var string */
    private $root;

    /**
     * @param Watchman $watchman
     * @param string   $root     Watched tree
     */
    public function __construct(Watchman $watchman, $root)
    {
        $this->watchman = $watchman;
        $this->root = $root;
    }

    /**
     * @return Watchman
     */
    public function getWatchman()
    {
        return $this->watchman;
    }

    /**
     * @return string Watched tree
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Adds a trigger to the watch.
     *
     * @param string $name     Name of the trigger.
     * @param string $patterns Patterns
     * @param string $command  Command
     *
     * @return string Name of the added trigger.
     *
     * @throws \RuntimeException if the trigger could not be created.
     */
    public function addTrigger($name, $patterns, $command)
    {
        return $this->watchman->trigger($this->root, $name, $patterns, $command);
    }

    /**
     * Deletes the trigger with the given name from the watch.
     *
     * @param string $name Name of the trigger.
     *
     * @return boolean `true` if the trigger has been deleted.
     */
    public function deleteTrigger($name)
    {
        return $this->watchman->triggerDelete($this->root, $name);
    }

    /**
     * Lists the triggers from the watch.
     *
     * @return array List of triggers.
     */
    public function listTriggers()
    {
        return $this->watchman->triggerList($this->root);
    }
}
