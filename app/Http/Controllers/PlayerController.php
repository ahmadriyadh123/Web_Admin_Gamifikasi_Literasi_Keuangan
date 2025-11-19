<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    // API 32: List Semua Pemain
    public function index()
    {
        // Mengambil semua pemain diurutkan dari yang terbaru
        return response()->json(Player::orderBy('createdAt', 'desc')->get());
    }

    public function show($id)
    {
        // Mencari pemain berdasarkan PlayerId (bukan id auto-increment)
        $player = Player::where('PlayerId', $id)->first();

        if (!$player) {
            return response()->json(['message' => 'Player not found'], 404);
        }

        return response()->json($player);
    }
}