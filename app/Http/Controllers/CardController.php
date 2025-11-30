<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CardService;

class CardController extends Controller
{
    protected $cardService;

    public function __construct(CardService $cardService)
    {
        $this->cardService = $cardService;
    }

    /**
     * GET /card/risk/{risk_id}
     */
    public function getRiskCard(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            $result = $this->cardService->drawRiskCard(
                $user->player->PlayerId,
                $id
            );

            if (isset($result['error'])) {
                return response()->json($result, 404);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getChanceCard(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            $result = $this->cardService->drawChanceCard(
                $user->player->PlayerId,
                $id
            );

            if (isset($result['error'])) {
                return response()->json($result, 404);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /card/quiz/{quiz_id}
     */
    public function getQuizCard(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            $result = $this->cardService->getQuizCardDetail(
                $user->player->PlayerId,
                $id
            );

            if (isset($result['error'])) {
                return response()->json($result, 404);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function submitQuiz(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'quiz_id' => 'required|string|exists:quiz_cards,id',
            'selected_option' => 'required|string',
            'decision_time_seconds' => 'numeric'
        ]);

        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            // 2. Panggil Service
            $result = $this->cardService->submitQuizAnswer(
                $user->player->PlayerId,
                $request->all()
            );

            if (isset($result['error'])) {
                return response()->json($result, 400);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}