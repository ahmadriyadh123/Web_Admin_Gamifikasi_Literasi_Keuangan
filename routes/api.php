<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ProfilingController;
use App\Http\Controllers\MatchmakingController;
use App\Http\Controllers\ScenarioController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\LeaderboardController;
use App\Repositories\ProfilingRepository;
use App\Services\ProfilingService;
use App\Services\AI\FuzzyService;
use App\Services\AI\FuzzyRule;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/debug/profiling/answer', function (
    Request $request,
    ProfilingRepository $repo
) {
    $data = $request->validate([
        'player_id' => 'required|string',
        'question_code' => 'required|string',
        'option_code' => 'required|string',
    ]);

    $question = $repo->getQuestionByCode($data['question_code']);

    if (!$question) {
        return response()->json(['error' => 'Question not found'], 404);
    }

    $repo->saveAnswer(
        $data['player_id'],
        $question->id,
        $data['option_code']
    );

    return response()->json([
        'status' => 'saved',
        'player_id' => $data['player_id'],
        'question_code' => $data['question_code'],
        'option_code' => $data['option_code'],
    ]);
});

Route::get('/debug/profiling/answers/{playerId}', function (
    $playerId,
    ProfilingRepository $repo
) {
    return response()->json(
        $repo->getAnswersByPlayerId($playerId)
    );
});

Route::get('/debug/profiling/features/{playerId}', function (
    $playerId,
    ProfilingService $service
) {
    return response()->json([
        'player_id' => $playerId,
        'features' => $service->calculateFeaturesFromAnswers($playerId),
    ]);
});

Route::post('/profiling/onboarding', function (
    Request $request,
    ProfilingService $profilingService
) {
    $data = $request->validate([
        'player_id' => 'required|string',
        'answers' => 'required|array',
        'profiling_done' => 'sometimes|boolean',
    ]);

    $result = $profilingService->saveOnboardingAnswers($data);

    return response()->json([
        'ok' => true,
        'message' => 'Onboarding answers saved',
        'profiling_result' => $result['profiling_result'] ?? null
    ]);
});

Route::get('/profiling/test/{playerId}', function (
    string $playerId,
    ProfilingService $profilingService,
    FuzzyService $fuzzyService,
) {
    $features = $profilingService->calculateFeaturesFromAnswers($playerId);

    if (empty($features)) {
        return response()->json([
            'error' => 'No features found'
        ], 404);
    }

    $fuzzy = $fuzzyService->categorize($features);

    return response()->json([
        'player_id' => $playerId,
        'numeric_features' => $features,
        'fuzzy_categories' => $fuzzy,
    ]);
});

Route::post('/debug/fuzzy/profiling', function (
    Request $request,
    FuzzyService $fuzzyService
) {
    $data = $request->validate([
        'player_id' => 'required|string',
        'features'  => 'required|array',
        'debug'     => 'required|boolean',

        // validasi minimal feature numeric
        'features.*' => 'required|numeric|min:0|max:100',
    ]);

    // Jalankan FUZZY SAJA
    $result = $fuzzyService->categorize(
        $data['player_id'],
        $data['features'],
        $data['debug']
    );

    return response()->json([
        'status' => 'ok',
        'message' => 'Fuzzy profiling test (ANN not executed)',
        'result' => $result,
    ]);
});


Route::prefix('auth')->group(function () {
    Route::post('/google', [AuthController::class, 'google']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

Route::prefix('config')->group(function () {
    Route::get('/game', [ConfigController::class, 'game']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('profiling')->group(function () {
        Route::get('/status', [ProfilingController::class, 'status']);
        Route::get('/questions', [ProfilingController::class, 'questions']);
        Route::post('/submit', [ProfilingController::class, 'submit']);
        Route::get('/cluster', [ProfilingController::class, 'cluster']);
    });
    
    Route::prefix('matchmaking')->group(function () {
        Route::post('/join', [MatchmakingController::class, 'join']);
        Route::post('/character/select', [MatchmakingController::class, 'selectCharacter']);
        Route::get('/status', [MatchmakingController::class, 'status']);
        Route::post('/ready', [MatchmakingController::class, 'ready']);
    });
    
    Route::prefix('session')->group(function () {
        Route::get('/state', [SessionController::class, 'state']);
        Route::post('/ping', [SessionController::class, 'ping']);
        Route::prefix('turn')->group(function () {
            Route::post('/start', [SessionController::class, 'startTurn']);
            Route::post('/roll', [SessionController::class, 'roll']);
            Route::get('/current', [SessionController::class, 'currentTurn']);
            Route::post('/end', [SessionController::class, 'endTurn']);
        });
        Route::post('/player/move', [SessionController::class, 'move']);
    });

    Route::get('/tile/{id}', [BoardController::class, 'getTile']);
    
    Route::prefix('scenario')->group(function () {
        Route::get('/{scenario_id}', [ScenarioController::class, 'show']);
        Route::post('/submit', [ScenarioController::class, 'submit']);
    });
    Route::prefix('card')->group(function () {
        Route::get('/risk/{risk_id}', [CardController::class, 'getRiskCard']);
        Route::get('/chance/{chance_id}', [CardController::class, 'getChanceCard']);
        Route::get('/quiz/{quiz_id}', [CardController::class, 'getQuizCard']);
        Route::post('/quiz/submit', [CardController::class, 'submitQuiz']);
    });
    
    Route::prefix('recommendation')->group(function () {
        Route::get('/next', [RecommendationController::class, 'next']);
        Route::get('/path', [RecommendationController::class, 'path']);
        Route::get('/peer', [RecommendationController::class, 'peer']);
    });
    
    Route::post('/feedback/intervention', [FeedbackController::class, 'store']);
    Route::get('/intervention/trigger', [InterventionController::class, 'trigger']);
    Route::get('/performance/scores', [PerformanceController::class, 'scores']);
    Route::get('/leaderboard', [LeaderboardController::class, 'getLeaderboard']);
});
