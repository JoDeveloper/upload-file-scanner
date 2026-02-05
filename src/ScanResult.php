<?php

namespace Jodeveloper\UploadFileScanner;

readonly class ScanResult
{
    public function __construct(
        public bool $clean,
        public string $output = '',
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
