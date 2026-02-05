<?php

namespace Jodeveloper\UploadFileScanner\Contracts;

use Jodeveloper\UploadFileScanner\ScanResult;

interface Scanner
{
    /**
     * Scan the given file path for malware.
     */
    public function scan(string $path): ScanResult;
}
