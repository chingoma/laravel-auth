<?php

namespace Lockminds\LaravelAuth;

use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Lockminds\LaravelAuth\Commands\LaravelAuthCommand;

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
            ->name('laravel-auth')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-auth_table')
            ->hasCommand(LaravelAuthCommand::class);
    }

    public function boot(): void
    {
        // ... other things
        $this->registerRoutes();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
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
            'prefix' => config('laravel-auth.prefix'),
        ];
    }
}
