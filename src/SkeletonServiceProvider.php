<?php

namespace Jodeveloper\UploadFileScanner;

use Illuminate\Support\Facades\Validator;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SkeletonServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('clamav-scanner')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ClamAvScanner::class, function () {
            return new ClamAvScanner(
                binary: config('clamav-scanner.binary'),
                timeout: config('clamav-scanner.timeout'),
                scanOptions: config('clamav-scanner.scan_options'),
            );
        });
    }

    public function packageBooted(): void
    {
        Validator::extend('clean_file', function ($attribute, $value, $parameters, $validator) {
            $rule = new Rules\CleanFile(app(ClamAvScanner::class));

            return $rule->passes($attribute, $value);
        }, 'The uploaded file contains a virus.');
    }
}
