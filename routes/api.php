<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;
use App\Http\Controllers\AuthController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function() {
    return response('Welcome to Travel Planner REST API with Laravel', 200);
});

Route::controller(AuthController::class)->group(function() {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(TripController::class)->group(function() {
        Route::get('/trips','list');
        Route::post('/trips', 'create');
        Route::put('/trips/{id}', 'update');
        Route::delete('/trips/{id}', 'delete');
    });
});

