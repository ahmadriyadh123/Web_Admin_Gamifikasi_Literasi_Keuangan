<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfilingController;
use App\Http\Controllers\RecommendationController;

Route::get('/profiling/status', [ProfilingController::class, 'status']);
Route::post('/profiling/submit', [ProfilingController::class, 'submit']);
Route::get('/profiling/cluster/{playerId}', [ProfilingController::class, 'cluster']);

Route::get('/recommendation/next', [RecommendationController::class, 'next']);

