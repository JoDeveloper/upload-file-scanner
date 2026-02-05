<?php

namespace Jodeveloper\UploadFileScanner\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Jodeveloper\UploadFileScanner\ScanResult scan(string $path)
 *
 * @see \Jodeveloper\UploadFileScanner\Contracts\Scanner
 */
class Scanner extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Jodeveloper\UploadFileScanner\Contracts\Scanner::class;
    }
}
