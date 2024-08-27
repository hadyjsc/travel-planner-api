<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function() {
    return response('Welcome to Travel Planner REST API with Laravel', 200);
});

Route::controller(TripController::class)->group(function() {
    Route::get('/trips','list');
    Route::post('/trips', 'create');
    Route::put('/trips/{id}', 'update');
    Route::delete('/trips/{id}', 'delete');
});

