<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\TripInterface;
use App\Interfaces\UserInterface;
use App\UseCases\TripUseCase;
use App\UseCases\UserUseCase;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TripInterface::class, TripUseCase::class);
        $this->app->bind(UserInterface::class, UserUseCase::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
