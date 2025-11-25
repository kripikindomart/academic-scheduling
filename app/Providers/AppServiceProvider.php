<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\StudentService;
use App\Services\LecturerService;
use App\Services\RoomService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StudentService::class, function ($app) {
            return new StudentService();
        });

        $this->app->singleton(LecturerService::class, function ($app) {
            return new LecturerService();
        });

        $this->app->singleton(RoomService::class, function ($app) {
            return new RoomService();
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
