<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Sunmi\SunmiOpenApi;

class SunmiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('sunmi', function () {
            return new SunmiOpenApi();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
