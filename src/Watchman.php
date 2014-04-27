<?php

namespace Cocur\Watchman;

use Symfony\Component\Process\Process;
use Cocur\Watchman\Process\ProcessFactory;

/**
 * Watchman
 */
class Watchman
{
    /** @var string */
    private $binary = 'watchman';

    /** @var ProcessFactory */
    private $processFactory;

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        $this->processFactory = new ProcessFactory();
    }

    /**
     * @param string $binary Path to the watchman binary.
     *
     * @return Watchman
     */
    public function setBinary($binary)
    {
        $this->binary = $binary;

        return $this;
    }

    /**
     * @return string Path to the watchman binary.
     */
    public function getBinary()
    {
        return $this->binary;
    }

    /**
     * @param ProcessFactory $processFactory
     *
     * @return Watchman
     */
    public function setProcessFactory(ProcessFactory $processFactory)
    {
        $this->processFactory = $processFactory;

        return $this;
    }

    /**
     * @return ProcessFactory
     */
    public function getProcessFactory()
    {
        return $this->processFactory;
    }

    /**
     * Executes the `watch` command.
     *
     * @param string $directory Directory to watch.
     *
     * @return boolean `true` if watcher was started successfully.
     *
     * @throws \RuntimeException when the watcher could not be created.
     */
    public function watch($directory)
    {
        $process = $this->processFactory->create(sprintf('%s watch %s', $this->getBinary(), $directory));

        return $this->runProcess($process);
    }

    /**
     * Executes the `trigger` command.
     * @param string $directory Directory
     * @param string $name      Trigger name
     * @param string $patterns  Patterns
     * @param string $command   Command to execute
     *
     * @return boolean `true` iff trigger was added successfully.
     *
     * @throws \RuntimeException if the trigger could not be created.
     */
    public function addTrigger($directory, $name, $patterns, $command)
    {
        $process = $this->processFactory->create(sprintf(
            '%s -- trigger %s %s %s -- %s',
            $this->binary,
            $directory,
            $name,
            $patterns,
            $command
        ));

        return $this->runProcess($process);
    }

    /**
     * @param Process $process
     *
     * @return boolean `true` iff processed runned without errors.
     *
     * @throws \RuntimeException when the watcher could not be created.
     */
    protected function runProcess(Process $process)
    {
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return true;
    }
}
