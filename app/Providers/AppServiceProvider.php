<?php

namespace App\Providers;

use App\Repositories\RoomRepository;
use App\Repositories\CinemaRepository;

use App\Repositories\SeanceRepository;
use Illuminate\Support\ServiceProvider;

use App\Interfaces\RoomRepositoryInterface;
use App\Repositories\ReservationRepository;
use App\Interfaces\CinemaRepositoryInterface;
use App\Interfaces\SeanceRepositoryInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Interfaces\ReservationRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CinemaRepositoryInterface::class, CinemaRepository::class);
        $this->app->bind(RoomRepositoryInterface::class, RoomRepository::class);
        $this->app->bind(SeanceRepositoryInterface::class, SeanceRepository::class);
        $this->app->bind(ReservationRepositoryInterface::class, ReservationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // JsonResource::withoutWrapping();
    }
}
