<?php

namespace Gemini\ValetLinuxPlusPlus;

use Symfony\Component\Process\Process;

class CommandLine
{
    public function run(string $command, ?callable $callback = null)
    {
        $process = Process::fromShellCommandline($command);

        $process->run($callback);

        return $process->getOutput();
    }

    public function runAsUser(string $command, ?callable $callback = null)
    {
        return $this->run($command, $callback);
    }
}
