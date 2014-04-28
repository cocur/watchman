<?php

namespace Cocur\Watchman;

use Closure;
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
    public function addWatch($directory)
    {
        $process = $this->processFactory->create(sprintf('%s watch %s', $this->getBinary(), $directory));

        return new Watch($this, $this->runProcess($process)['watch']);
    }

    /**
     * Executes the `watch-list` command.
     * +
     * @return string[] List of roots.
     */
    public function listWatches()
    {
        $process = $this->processFactory->create(sprintf('%s watch-list', $this->getBinary()));

        $rootNames = $this->runProcess($process)['roots'];
        $roots = [];
        foreach ($rootNames as $rootName) {
            $roots[] = new Watch($this, $rootName);
        }

        return $roots;
    }

    /**
     * Executes the `watch-del` command.
     *
     * @param string $directory
     *
     * @return boolean `true` if the watch has been deleted.
     */
    public function deleteWatch($directory)
    {
        $process = $this->processFactory->create(sprintf('%s watch-del %s', $this->getBinary(), $directory));

        return (bool)$this->runProcess($process)['watch-del'];
    }

    /**
     * Executes the `trigger` command.
     * @param Watch|string $watch     Watch object or path to root.
     * @param string       $name      Trigger name
     * @param string       $patterns  Patterns
     * @param string       $command   Command to execute
     *
     * @return string Name of the added trigger.
     *
     * @throws \RuntimeException if the trigger could not be created.
     */
    public function addTrigger($watch, $name, $patterns, $command)
    {
        if ($watch instanceof Watch) {
            $root = $watch->getRoot();
        } else {
            $root = $watch;
            $watch = new Watch($this, $root);
        }

        $process = $this->processFactory->create(sprintf(
            '%s -- trigger %s %s %s -- %s',
            $this->binary,
            $root,
            $name,
            $patterns,
            $command
        ));

        return new Trigger($watch, $this->runProcess($process)['triggerid']);
    }

    /**
     * Executes the `trigger-list` command.
     *
     * @param Watch|string $directory Directory
     *
     * @return array List of triggers.
     */
    public function listTriggers($watch)
    {
        if ($watch instanceof Watch) {
            $root = $watch->getRoot();
        } else {
            $root = $watch;
            $watch = new Watch($this, $root);
        }

        $process = $this->processFactory->create(sprintf('%s trigger-list %s', $this->getBinary(), $root));

        $triggerNames = $this->runProcess($process)['triggers'];
        $triggers = [];
        foreach ($triggerNames as $triggerData) {
            $triggers[] = new Trigger($watch, $triggerData['name'], $triggerData);
        }

        return $triggers;
    }

    /**
     * Executes the `trigger-delete` command.
     *
     * @param Watch|string $watch Watch or directory name.
     * @param string       $name  Trigger name.
     *
     * @return boolean `true` if the trigger has been deleted.
     */
    public function deleteTrigger($root, $name)
    {
        if ($root instanceof Watch) {
            $root = $root->getRoot();
        }

        $process = $this->processFactory->create(
            sprintf('%s trigger-del %s %s', $this->getBinary(), $root, $name)
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
     * Executes the `get-sockname` command.
     *
     * @return string Name of the socket Watchman listens to.
     */
    public function getSockname()
    {
        $process = $this->processFactory->create(sprintf('%s get-sockname', $this->getBinary()));

        return $this->runProcess($process)['sockname'];
    }

    /**
     * Executes the `clock` command.
     *
     * @param Watch|string $root Watch object or root directory.
     *
     * @return string Clock
     */
    public function getClock($root)
    {
        if ($root instanceof Watch) {
            $root = $root->getRoot();
        }

        $process = $this->processFactory->create(sprintf('%s clock %s', $this->getBinary(), $root));

        return $this->runProcess($process)['clock'];
    }

    /**
     * Executes the `find` command.
     *
     * @param Watch|string $root    Watch object or root directory.
     * @param string       $pattern Pattern
     *
     * @return array[] List of files that match the pattern
     */
    public function find($root, $pattern)
    {
        if ($root instanceof Watch) {
            $root = $root->getRoot();
        }

        $process = $this->processFactory->create(sprintf('%s find %s %s', $this->getBinary(), $root, $pattern));

        return $this->runProcess($process)['files'];
    }

    /**
     * Executes the `log` command.
     *
     * @param string $level   Log level (debug|error)
     * @param string $message Message to log
     *
     * @return boolean `true` if the message has been logged.
     */
    public function log($level, $message)
    {
        if (!in_array($level, ['debug', 'error'])) {
            throw new \InvalidArgumentException(
                sprintf('Log level "%s" does not exist. Must be one of: debug, error', $level)
            );
        }

        $process = $this->processFactory->create(sprintf('%s log %s "%s"', $this->getBinary(), $level, $message));

        return (bool)$this->runProcess($process)['logged'];
    }

    /**
     * Executes the `log-level` command.
     *
     * @param string  $logLevel Log level to watch (debug|error|off)
     * @param Closure $callback Function to call when new message is received.
     *
     * @return void
     *
     * @see examples/log-level.php
     */
    public function watchLogByLevel($logLevel, Closure $callback)
    {
        if (!in_array($logLevel, ['debug', 'error', 'off'])) {
            throw new \InvalidArgumentException(
                sprintf('Log level "%s" does not exist. Must be one of: debug, error, off', $logLevel)
            );
        }

        $process = $this->processFactory->create(
            sprintf('%s --server-encoding=json --persistent log-level %s', $this->getBinary(), $logLevel)
        );

        $process->run(function ($type, $buffer) use ($callback) {
            // @codeCoverageIgnoreStart
            if (Process::ERR === $type) {
                throw new \RuntimeException($buffer);
            } else {
                // Fix JSON
                $buffer = preg_replace('/\}(\s*)\{/', ',', $buffer);
                $message = Json::decode($buffer, true);
                isset($message['log']) ? $callback(trim($message['log'])) : null;
            }
            // @codeCoverageIgnoreEnd
        });
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
