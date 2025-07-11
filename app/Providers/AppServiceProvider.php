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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set the application timezone
        date_default_timezone_set(config('app.timezone'));
        // Ensure Carbon uses the same timezone
        \Carbon\Carbon::setToStringFormat('Y-m-d H:i:s');
    }
}
