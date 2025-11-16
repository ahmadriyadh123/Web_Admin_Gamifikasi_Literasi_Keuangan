<?php

namespace App\Services;

use App\Repositories\PlayerProfileRepository;

class ThresholdService
{
    protected $profileRepository;

    // Inject Repository ke dalam Service
    public function __construct(PlayerProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * Ini adalah implementasi "getPlayerThresholds"
     * dari sequence diagram Anda.
     */
    public function getPlayerThresholds(string $playerId)
    {
        // 1. Panggil Repository
        $profile = $this->profileRepository->findThresholdsByPlayerId($playerId);

        // 2. Jika pemain tidak ditemukan
        if (!$profile) {
            return null;
        }

        // 3. Format data (sesuai API 29)
        return [
            'player_id' => $profile->PlayerId,
            'thresholds' => $profile->thresholds
        ];
    }
}