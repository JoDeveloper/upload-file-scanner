<?php

namespace Jodeveloper\UploadFileScanner\Contracts;

use Jodeveloper\UploadFileScanner\ScanResult;

interface Scanner
{
    /**
     * Scan the given file path for malware.
     *
     * @param  string  $path
     * @return ScanResult
     */
    public function scan(string $path): ScanResult;
}
