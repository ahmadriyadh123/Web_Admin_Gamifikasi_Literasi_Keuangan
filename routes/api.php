<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeaderboardController; // <-- Impor Controller Anda
use App\Http\Controllers\Api\ThresholdController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rute untuk API 31 (Publik untuk tes)
// API 29 - Threshold
Route::get('/threshold', [ThresholdController::class, 'getThresholds']);
Route::get('/leaderboard', [LeaderboardController::class, 'getLeaderboard']);