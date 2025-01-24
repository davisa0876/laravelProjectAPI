<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CrawlerController;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\App;
use App\Http\Middleware\LanguageMiddleware;

// Test route with language middleware
Route::middleware([LanguageMiddleware::class])->group(function () {
    Route::get('/hello', function () {
        return response()->json([
            'message' => 'Hello, API!',
            'locale' => App::getLocale()
        ]);
    });
});

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
});

// Language Route
Route::post('/language', [LanguageController::class, 'switchLang']);

// Weather Route
Route::get('/weather', [WeatherController::class, 'show']);

Route::post('/crawl', [CrawlerController::class, 'crawl']);
