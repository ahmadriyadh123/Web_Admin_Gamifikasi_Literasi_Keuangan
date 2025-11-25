<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RecommendationService;

class RecommendationController extends Controller
{
    protected $recommendationService;

    // Inject RecommendationService
    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Mendapatkan rekomendasi berdasarkan player_id
     */
    public function next(Request $request)
    {
        $playerId = $request->query('player_id');
        if (!$playerId) return response()->json(['error' => 'player_id required'], 400);

        $result = $this->recommendationService->recommendNextQuestion($playerId);
        
        return response()->json($result);
    }
}
