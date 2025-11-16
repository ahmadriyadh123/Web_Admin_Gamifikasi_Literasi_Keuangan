<?php

namespace App\Repositories;

use App\Models\PlayerProfile;

class PlayerProfileRepository
{
    /**
     * Ini adalah implementasi "findThresholdsByPlayerId"
     * dari sequence diagram Anda.
     */
    public function findThresholdsByPlayerId(string $playerId)
    {
        // Menggunakan Model Eloquent untuk mengambil data
        return PlayerProfile::where('PlayerId', $playerId)
            ->select('PlayerId', 'thresholds')
            ->first();
    }
}