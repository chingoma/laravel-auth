<?php

namespace Lockminds\LaravelAuth;

use Illuminate\Support\Facades\Route;
use Lockminds\LaravelAuth\Commands\LaravelAuthCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelAuthServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('lockminds-auth')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommand(LaravelAuthCommand::class);
    }

    public function boot(): void
    {
        // ... other things
        $this->registerRoutes();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }

    protected function routeConfiguration(): array
    {
        return [
            'prefix' => config('lockminds-auth.route_prefix'),
        ];
    }
}
