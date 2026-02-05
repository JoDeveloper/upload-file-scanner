<?php

namespace Jodeveloper\UploadFileScanner\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Jodeveloper\UploadFileScanner\Contracts\Scanner;

class CleanFile implements ValidationRule
{
    public function __construct(
        protected ?Scanner $scanner = null,
    ) {
        $this->scanner = $scanner ?? app(Scanner::class);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $path = $value->getRealPath();

        if ($path === false) {
            $fail('The uploaded file contains a virus.');

            return;
        }

        $result = $this->scanner->scan($path);

        if (! $result->isClean()) {
            $fail('The uploaded file contains a virus.');
        }
    }
}
