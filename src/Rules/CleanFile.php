<?php

namespace Jodeveloper\UploadFileScanner\Rules;

use Illuminate\Contracts\Validation\Rule;
use Jodeveloper\UploadFileScanner\ClamAvScanner;

class CleanFile implements Rule
{
    public function __construct(
        private ClamAvScanner $scanner,
    ) {}

    public function passes($attribute, $value): bool
    {
        $path = $value->getRealPath();

        if ($path === false) {
            return false;
        }

        $result = $this->scanner->scan($path);

        return $result->isClean();
    }

    public function message(): string
    {
        return 'The uploaded file contains a virus.';
    }
}
