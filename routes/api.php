<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\SeanceController;
use App\Http\Controllers\ReservationController;

Route::get('/health', function () {
    return response()->json(['uptime' => now()->diffInSeconds(config('app.started_at'))], 200);
});

Route::apiResource('/cinemas', CinemaController::class);

Route::apiResource('/cinemas/{cinemaId}/rooms', RoomController::class);
    // ->middleware('checkAdminRole', ['only' => ['store', 'update', 'destroy']]);

Route::apiResource('/cinemas/{cinemaId}/rooms/{roomId}/seances', SeanceController::class);
    // ->middleware('checkAdminRole', ['only' => ['store', 'update', 'destroy']]);


Route::get('/movie/{movieId}/reservations', [ReservationController::class, 'index'])
    ->name('reservations.index');

Route::post('/movie/{movieId}/reservations', [ReservationController::class, 'store'])
    ->name('reservations.store');

Route::get('/reservations/{reservationId}/confirm', [ReservationController::class, 'confirm'])
    ->name('reservations.confirm');

Route::get('/reservations/{reservationId}', [ReservationController::class, 'show'])
    ->name('reservations.show')
    ->middleware('checkAdminRole');

Route::put('/reservations/{reservationId}', [ReservationController::class, 'update'])
    ->name('reservations.update');

Route::delete('/reservations/{reservationId}', [ReservationController::class, 'destroy'])
    ->name('reservations.destroy')
    ->middleware('checkAdminRole');



