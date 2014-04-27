<?php

namespace Cocur\Watchman\Process;

use Symfony\Component\Process\Process;

/**
 * ProcessFactory
 */
class ProcessFactory
{
    /**
     * Creates a new instance of {@see Symfony\Component\Process\Process}.
     *
     * @param string $command Command to initialize the process with.
     *
     * @return Symfony\Component\Process\Process
     */
    public function create($command)
    {
        return new Process($command);
    }
}
