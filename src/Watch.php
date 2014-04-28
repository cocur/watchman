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
     * Deletes the watch.
     *
     * @return boolean `true` if the watch has been deleted.
     */
    public function delete()
    {
        return $this->watchman->deleteWatch($this->root);
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
        return $this->watchman->addTrigger($this->root, $name, $patterns, $command);
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
        return $this->watchman->deleteTrigger($this->root, $name);
    }

    /**
     * Lists the triggers from the watch.
     *
     * @return array List of triggers.
     */
    public function listTriggers()
    {
        return $this->watchman->listTriggers($this->root);
    }

    /**
     * Returns the clock of the watch.
     *
     * @return string Clock of the watch.
     */
    public function getClock()
    {
        return $this->watchman->getClock($this->root);
    }

    /**
     * Finds the files in the watch that match the given pattern.
     *
     * @param string $pattern Pattern.
     *
     * @return array[] List of files that match the pattern.
     */
    public function find($pattern)
    {
        return $this->watchman->find($this->root, $pattern);
    }
}
