<?php

namespace Jodeveloper\UploadFileScanner;

use Illuminate\Support\Facades\Validator;
use Jodeveloper\UploadFileScanner\Contracts\Scanner;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class UploadFileScannerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('clamav-scanner')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Scanner::class, function () {
            return new ClamAvScanner(
                binary: config('clamav-scanner.binary'),
                timeout: config('clamav-scanner.timeout'),
                scanOptions: config('clamav-scanner.scan_options'),
            );
        });

        $this->app->alias(Scanner::class, ClamAvScanner::class);
    }

    public function packageBooted(): void
    {
        Validator::extend('clean_file', function ($attribute, $value, $parameters, $validator) {
            $rule = new Rules\CleanFile(app(Scanner::class));
            $passes = true;

            $rule->validate($attribute, $value, function ($message) use (&$passes) {
                $passes = false;
            });

            return $passes;
        }, 'The uploaded file contains a virus.');
    }
}
