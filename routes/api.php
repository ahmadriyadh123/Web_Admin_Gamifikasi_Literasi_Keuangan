<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfilingController;
use App\Http\Controllers\RecommendationController;

Route::prefix('auth')->group(function () {
    Route::post('/google', [App\Http\Controllers\AuthController::class, 'google']);
    Route::post('/refresh', [App\Http\Controllers\AuthController::class, 'refresh']);
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('profiling')->group(function () {
        Route::get('/status', [ProfilingController::class, 'status']);
        Route::post('/submit', [ProfilingController::class, 'submit']);
        Route::get('/cluster/{playerId}', [ProfilingController::class, 'cluster']);
    });
    
    Route::prefix('recommendation')->group(function () {
        Route::get('/next', [RecommendationController::class, 'next']);
        Route::get('/path', [RecommendationController::class, 'path']);
        Route::get('/peer', [RecommendationController::class, 'peer']);
    });
});
