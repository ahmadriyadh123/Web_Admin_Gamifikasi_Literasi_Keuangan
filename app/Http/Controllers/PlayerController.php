<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PlayerService;

class PlayerController extends Controller
{
    protected $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    /**
     * Login dengan Google
     * POST /auth/google
     */
    public function googleLogin(Request $request)
    {
        $data = $request->validate([
            'google_id_token' => 'required|string',
            'platform' => 'nullable|string',
            'locale' => 'nullable|string'
        ]);

        try {
            $result = $this->playerService->loginWithGoogle($data);
            
            return response()->json($result, 200);

        } catch (\Exception $e) {
            // Tangani error jika token Google tidak valid
            return response()->json([
                'error' => 'Authentication Failed',
                'message' => $e->getMessage()
            ], 401);
        }
    }
}