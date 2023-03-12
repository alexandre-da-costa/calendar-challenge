<?php

namespace App\Providers;

use App\Interfaces\CalendarInterface;
use App\Interfaces\PersonEnrichmentInterface;
use App\Services\UserGems\CalendarService;
use App\Services\UserGems\PersonDataService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        app()->bind(CalendarInterface::class, CalendarService::class);
        app()->bind(PersonEnrichmentInterface::class, PersonDataService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
