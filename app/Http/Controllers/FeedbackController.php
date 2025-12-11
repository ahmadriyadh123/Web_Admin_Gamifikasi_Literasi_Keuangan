<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\FeedbackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// Force load to bypass potential autoloader cache issues
require_once app_path('Services/FeedbackService.php');

class FeedbackController extends Controller
{
    protected $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'intervention_id' => 'required|string',
            'scenario_id' => 'required|string',
            'player_response' => 'required|string'
        ]);

        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            $result = $this->feedbackService->storeInterventionFeedback(
                $user->player->PlayerId,
                $validated
            );

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}