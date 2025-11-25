<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\LeaderboardController;

// Main routes
Route::get('/', function () {
    return view('admin.dashboard');
});

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard');

// Leaderboard (alias lama)
Route::get('/leaderboard', function () {
    return view('admin.players.leaderboard');
})->name('leaderboard');

// Admin config routes
Route::get('/admin/config', function () {
    return view('admin.config.index');
})->name('admin.config');

Route::get('/admin/config/edit', function () {
    return view('admin.config.edit');
})->name('admin.config.edit');

Route::get('/admin/config/sync', function () {
    return view('admin.config.sync');
})->name('admin.config.sync');

// Admin players leaderboard
Route::get('/admin/players/leaderboard', function () {
    return view('admin.players.leaderboard');
})->name('admin.players.leaderboard');

// Admin content management routes
Route::get('/admin/content/scenarios', function () {
    return view('admin.content.scenarios');
})->name('admin.content.scenarios');

Route::get('/admin/content/cards', function () {
    return view('admin.content.cards');
})->name('admin.content.cards');

Route::get('/admin/content/quiz', function () {
    return view('admin.content.quiz');
})->name('admin.content.quiz');

// Admin login routes
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');

// Player management
Route::get('/admin/players', [PlayerController::class, 'index'])->name('admin.players');
Route::get('/admin/players/{id}/profiling', [PlayerController::class, 'profilingView'])->name('admin.players.profiling');

// Lightweight profiling API
Route::get('/profiling/details', [PlayerController::class, 'profilingDetails'])->name('profiling.details');
Route::get('/profiling/cluster', [PlayerController::class, 'profilingCluster'])->name('profiling.cluster');
Route::get('/api/players', [PlayerController::class, 'apiPlayers'])->name('api.players');

// Rekomendasi lanjutan
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('rekomendasi-lanjutan', [PlayerController::class, 'rekomendasiIndex'])->name('rekomendasi.index');
});

Route::post('recommendation/next', [PlayerController::class, 'recommendationNext'])->name('recommendation.next');

// Learning Path
Route::get('admin/learning_path', [PlayerController::class, 'learningPathIndex'])->name('admin.learning-path.index');

Route::get('admin/learning-path', function () {
    return view('admin.learning_path.index');
})->name('admin.learning-path.index');

// Peer Insight
Route::view('admin/peer-insight', 'admin.peer_insight.index')->name('admin.peer-insight.index');
