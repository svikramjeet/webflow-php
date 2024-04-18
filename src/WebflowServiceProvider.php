<?php

namespace Svikramjeet\Webflow;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Svikramjeet\Webflow\Commands\WebflowCommand;

class WebflowServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('webflow')
            ->hasConfigFile();
    }
}
