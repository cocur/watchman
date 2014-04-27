<?php

namespace Cocur\Watchman;

use Braincrafted\Json\Json;
use Cocur\Watchman\Process\ProcessFactory;
use Symfony\Component\Process\Process;

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
     * @return string Name of the watched directory.
     *
     * @throws \RuntimeException when the watcher could not be created.
     */
    public function watch($directory)
    {
        $process = $this->processFactory->create(sprintf('%s watch %s', $this->getBinary(), $directory));

        return $this->runProcess($process)['watch'];
    }

    /**
     * Executes the `trigger` command.
     * @param string $directory Directory
     * @param string $name      Trigger name
     * @param string $patterns  Patterns
     * @param string $command   Command to execute
     *
     * @return string Name of the added trigger.
     *
     * @throws \RuntimeException if the trigger could not be created.
     */
    public function trigger($directory, $name, $patterns, $command)
    {
        $process = $this->processFactory->create(sprintf(
            '%s -- trigger %s %s %s -- %s',
            $this->binary,
            $directory,
            $name,
            $patterns,
            $command
        ));

        return $this->runProcess($process)['triggerid'];
    }

    /**
     * @param Process $process
     *
     * @return array JSON-decoded output of the watchman result.
     *
     * @throws \RuntimeException when the watcher could not be created.
     */
    protected function runProcess(Process $process)
    {
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return Json::decode($process->getOutput(), true);
    }
}
