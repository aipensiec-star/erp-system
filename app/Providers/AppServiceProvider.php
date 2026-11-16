<?php

namespace App\Providers;

use App\Services\InstallerService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(InstallerService::class);
        $this->app->alias(InstallerService::class, 'installer');
    }

    public function boot(): void
    {
        //
    }
}
