<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind('rates-api-url', function () {
        return config('app.rates', env('RATES_API_URL'));
    });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //

    }
}
