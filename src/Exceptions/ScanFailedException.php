<?php

namespace Jodeveloper\UploadFileScanner\Exceptions;

use Exception;

class ScanFailedException extends Exception
{
    public static function make(string $command, string $output, int $exitCode): self
    {
        return new self(
            sprintf(
                'ClamAV scan failed. Command: %s, Output: %s, Exit code: %d',
                $command,
                $output,
                $exitCode
            )
        );
    }
}
