<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\TripInterface;
use App\UseCases\TripUseCase;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TripInterface::class, TripUseCase::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
