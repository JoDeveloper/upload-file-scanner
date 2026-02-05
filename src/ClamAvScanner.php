<?php

namespace Jodeveloper\UploadFileScanner;

use Jodeveloper\UploadFileScanner\Contracts\Scanner;
use Jodeveloper\UploadFileScanner\Exceptions\ScanFailedException;
use Symfony\Component\Process\Process;

class ClamAvScanner implements Scanner
{
    public function __construct(
        private readonly string $binary,
        private readonly int $timeout,
        private readonly array $scanOptions,
    ) {}

    public function scan(string $path): ScanResult
    {
        $command = $this->buildCommand($path);

        $process = $this->createProcess($command);
        $process->setTimeout($this->timeout);

        $process->run();

        $exitCode = $process->getExitCode();

        // 0: No virus found
        if ($exitCode === 0) {
            return new ScanResult(
                clean: true,
                output: $process->getOutput(),
            );
        }

        // 1: Virus found
        if ($exitCode === 1) {
            return new ScanResult(
                clean: false,
                output: $process->getOutput(),
            );
        }

        $this->handleProcessFailure($process, $command, $exitCode);
    }

    protected function createProcess(array $command): Process
    {
        return new Process($command);
    }

    private function buildCommand(string $path): array
    {
        return array_merge(
            [$this->binary],
            $this->scanOptions,
            [$path]
        );
    }

    private function handleProcessFailure(Process $process, array $command, ?int $exitCode): never
    {
        throw ScanFailedException::make(
            implode(' ', $command),
            $process->getErrorOutput(),
            $exitCode
        );
    }
}
