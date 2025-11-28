<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;

class ConfigController extends Controller
{
    /**
     * GET /config/game
     * Mengambil konfigurasi permainan global.
     */
    public function game()
    {
        $config = Config::first();

        if (!$config) {
            return response()->json([
                'min_players' => 2,
                'max_players' => 5,
                'max_turns' => 50,
                'version' => 1
            ]);
        }

        return response()->json([
            'min_players' => $config->minPlayers,
            'max_players' => $config->maxPlayers,
            'max_turns'   => $config->max_turns
        ]);
    }
}