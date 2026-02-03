<?php

namespace Jodeveloper\UploadFileScanner;

use Symfony\Component\Process\Process;
use Jodeveloper\UploadFileScanner\Exceptions\ScanFailedException;

class ClamAvScanner
{
    public function __construct(
        private string $binary,
        private int $timeout,
        private array $scanOptions,
    ) {
    }

    public function scan(string $path): ScanResult
    {
        $command = $this->buildCommand($path);

        $process = new Process($command);
        $process->setTimeout($this->timeout);

        $process->run();

        $exitCode = $process->getExitCode();

        if ($exitCode === 0) {
            return new ScanResult(
                clean: true,
                output: $process->getOutput(),
            );
        }

        if ($exitCode === 1) {
            return new ScanResult(
                clean: false,
                output: $process->getOutput(),
            );
        }

        $this->handleProcessFailure($process, $command);
    }

    private function buildCommand(string $path): array
    {
        return array_merge(
            [$this->binary],
            $this->scanOptions,
            [$path]
        );
    }

    private function handleProcessFailure(Process $process, array $command): never
    {
        throw ScanFailedException::make(
            implode(' ', $command),
            $process->getErrorOutput(),
            $process->getExitCode() ?? -1
        );
    }
}
