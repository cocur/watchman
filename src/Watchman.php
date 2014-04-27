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

        return new Watch($this, $this->runProcess($process)['watch']);
    }

    /**
     * Executes the `watch-list` command.
     * +
     * @return string[] List of roots.
     */
    public function watchList()
    {
        $process = $this->processFactory->create(sprintf('%s watch-list', $this->getBinary()));

        return $this->runProcess($process)['roots'];
    }

    /**
     * Executes the `watch-del` command.
     *
     * @param string $directory
     *
     * @return boolean `true` if the watch has been deleted.
     */
    public function watchDelete($directory)
    {
        $process = $this->processFactory->create(sprintf('%s watch-del %s', $this->getBinary(), $directory));

        return (bool)$this->runProcess($process)['watch-del'];
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
     * Executes the `trigger-list` command.
     *
     * @param string $directory Directory
     *
     * @return array List of triggers.
     */
    public function triggerList($directory)
    {
        $process = $this->processFactory->create(sprintf('%s trigger-list %s', $this->getBinary(), $directory));

        return $this->runProcess($process)['triggers'];
    }

    /**
     * Executes the `trigger-delete` command.
     *
     * @param string $directory Directory
     * @param string $name      Trigger name.
     *
     * @return boolean `true` if the trigger has been deleted.
     */
    public function triggerDelete($directory, $name)
    {
        $process = $this->processFactory->create(
            sprintf('%s trigger-del %s %s', $this->getBinary(), $directory, $name)
        );

        return (bool)$this->runProcess($process)['deleted'];
    }

    /**
     * Executes the `shutdown-server` command.
     *
     * @return void
     */
    public function shutdownServer()
    {
        $process = $this->processFactory->create(sprintf('%s shutdown-server', $this->getBinary()));
        $this->runProcess($process);
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

        $output = Json::decode($process->getOutput(), true);

        if (!empty($output['error'])) {
            throw new \RuntimeException($output['error']);
        }

        return $output;
    }
}
