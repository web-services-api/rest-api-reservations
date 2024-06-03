<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\CinemaController;



Route::apiResource('/cinemas', CinemaController::class);

Route::apiResource('/cinemas/{cinemaId}/rooms', RoomController::class)->middleware('checkAdminRole', ['only' => ['store', 'update', 'destroy']]);



