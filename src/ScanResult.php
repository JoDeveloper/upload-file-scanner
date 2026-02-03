<?php

namespace Jodeveloper\UploadFileScanner;

readonly class ScanResult
{
    public function __construct(
        private bool $clean,
        private string $output,
    ) {}

    public function isClean(): bool
    {
        return $this->clean;
    }

    public function hasVirus(): bool
    {
        return ! $this->clean;
    }

    public function output(): string
    {
        return $this->output;
    }
}
