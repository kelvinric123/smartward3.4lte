<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\HL7Integration\HL7ListenerService;
use App\Services\HL7Integration\HL7ParserService;
use App\Services\HL7Integration\HL7AdmissionMapperService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register HL7 Integration Services
        $this->app->singleton(HL7ListenerService::class, function ($app) {
            return new HL7ListenerService(
                $app->make(HL7ParserService::class),
                $app->make(HL7AdmissionMapperService::class)
            );
        });

        $this->app->singleton(HL7ParserService::class, function ($app) {
            return new HL7ParserService();
        });

        $this->app->singleton(HL7AdmissionMapperService::class, function ($app) {
            return new HL7AdmissionMapperService();
        });
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
