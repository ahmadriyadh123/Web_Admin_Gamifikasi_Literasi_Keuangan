<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeaderboardController; // <-- Impor Controller Anda

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rute untuk API 31 (Publik untuk tes)
Route::get('/leaderboard', [LeaderboardController::class, 'getLeaderboard']);