<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\TravelHotelController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\TravelFormController;
use App\Http\Controllers\AviationFlightController;
use App\Http\Controllers\AuthController;

// Auth routes — publicly accessible
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes — only accessible if logged in
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Travel Form (already protected)
    Route::get('/travel-form', [TravelFormController::class, 'index']);
    Route::get('/travel-form/{id}', [TravelFormController::class, 'show']);
    Route::post('/travel-form', [TravelFormController::class, 'store']);
    Route::put('/travel-form/{id}', [TravelFormController::class, 'update']);
    Route::delete('/travel-form/{id}', [TravelFormController::class, 'destroy']);

    // Previously public APIs — now protected
    Route::get('/weather', [WeatherController::class, 'getWeather']);
    Route::get('/flights/search', [FlightController::class, 'search']);
    Route::get('/convert', [CurrencyController::class, 'convert']);
    Route::get('/places/city/{city}', [PlaceController::class, 'getPlacesByCity']);
    Route::get('/hotels/search', [TravelHotelController::class, 'searchHotels']);
    Route::get('/photos', [PhotoController::class, 'getPhotos']);
    Route::get('/find/flights', [AviationFlightController::class, 'search']);
});
