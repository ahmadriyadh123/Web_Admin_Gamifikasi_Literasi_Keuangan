<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\ProfilingSubmitRequest;
use App\Services\ProfilingService;

class ProfilingController extends Controller
{
    protected $profilingService;
    
    /**
     * Konstruktor untuk ProfilingController.
     */
    public function __construct(ProfilingService $profilingService)
    {
        $this->profilingService = $profilingService;
    }

    /**
     * Mendapatkan status profiling untuk player tertentu.
     */
    public function status(Request $request)
    {
        $result = $this->profilingService->getProfilingStatus($playerId);

        return response()->json($result);
    }

    /**
     * Menyimpan jawaban onboarding dari player.
     */
    public function submit(ProfilingSubmitRequest $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player not found'], 404);
        }
        $playerId = $user->player->PlayerId;
        $validatedData = $request->validated();
        $this->profilingService->saveOnboardingAnswers([
            'player_id' => $playerId,
            'answers' => $validatedData['answers'],
            'profiling_done' => $validatedData['profiling_done'] ?? false 
        ]);
        return response()->json(['ok' => true]);
    }

    /**
     * Menjalankan proses clustering profiling untuk player tertentu.
     */
    public function cluster($playerId)
    {
        $result = $this->profilingService->runProfilingCluster($playerId);
        return response()->json($result);
    }

}
