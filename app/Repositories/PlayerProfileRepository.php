<?php

namespace App\Repositories;

use App\Models\PlayerProfile;

class PlayerProfileRepository
{
    public function findThresholdsByPlayerId(string $playerId)
    {
        return PlayerProfile::where('PlayerId', $playerId)
            ->select('PlayerId', 'thresholds')
            ->first();
    }

    // --- FUNGSI DIPERBARUI ---
    // Sekarang menerima parameter $reason (opsional)
    public function updateThresholds(string $playerId, array $newThresholds, ?string $reason = null)
    {
        // 1. Siapkan data wajib
        $data = ['thresholds' => $newThresholds];

        // 2. Jika ada reason, tambahkan ke data update
        // Pastikan nama kolom sesuai dengan skema V6 Anda: 'last_threshold_update_reason'
        if ($reason) {
            $data['last_threshold_update_reason'] = $reason;
        }

        // 3. Lakukan Update
        return PlayerProfile::where('PlayerId', $playerId)
            ->update($data);
    }
}