<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\CategoryController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::get('/movies/search', [MovieController::class, 'search']);
Route::apiResource('/movies',MovieController::class);

Route::get('/categories/search', [CategoryController::class, 'search']);
Route::apiResource('/categories',CategoryController::class);

Route::apiResource('/medias',MediaController::class);
