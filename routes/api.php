<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\SeanceController;

Route::get('/health', function () {
    return response()->json(['uptime' => now()->diffInSeconds(config('app.started_at'))], 200);
});

Route::apiResource('/cinemas', CinemaController::class);

Route::apiResource('/cinemas/{cinemaId}/rooms', RoomController::class)
    ->middleware('checkAdminRole', ['only' => ['store', 'update', 'destroy']]);

Route::apiResource('/cinemas/{cinemaId}/rooms/{roomId}/seances', SeanceController::class)
    ->middleware('checkAdminRole', ['only' => ['store', 'update', 'destroy']]);




