<?php

namespace App\Repositories;

use App\Models\PlayerDecision;
use App\Models\Scenario;
use Illuminate\Support\Collection;

class InterventionRepository
{
    /**
     * Mengambil keputusan terakhir pemain
     */
    public function getRecentDecisions(string $playerId, int $limit = 15): Collection
    {
        return PlayerDecision::where('player_id', $playerId)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Menganalisis pola kesalahan pemain (Global & Per Kategori)
     * 
     * @param Collection $decisions
     * @return array ['global_errors' => int, 'category_streaks' => array]
     */
    public function analyzeStreaks(Collection $decisions): array
    {
        if ($decisions->isEmpty()) {
            return [
                'global_errors' => 0,
                'category_streaks' => []
            ];
        }

        $globalConsecutiveErrors = 0;
        $globalChainBroken = false;

        $categoryStreaks = []; // 'category_name' => streak_count
        $categoryBroken = [];  // 'category_name' => boolean

        foreach ($decisions as $decision) {
            // Kita hanya peduli pada scenario
            // Jika ada tipe lain, kita bisa pilih untuk ignore atau break global chain
            if ($decision->content_type !== 'scenario') {
                // Opsional: break global chain jika bukan scenario?
                // Untuk aman, kita biarkan saja (skip) atau anggap pemutus
                // $globalChainBroken = true; 
                continue;
            }

            // Global Streak
            if (!$globalChainBroken) {
                if (!$decision->is_correct) {
                    $globalConsecutiveErrors++;
                } else {
                    $globalChainBroken = true;
                }
            }

            // Category Streak
            $category = $this->getCategoryFromDecision($decision);

            if ($category) {
                // Init data jika baru ketemu kategori ini
                if (!isset($categoryBroken[$category])) {
                    $categoryBroken[$category] = false;
                    $categoryStreaks[$category] = 0;
                }

                if (!$categoryBroken[$category]) {
                    if (!$decision->is_correct) {
                        $categoryStreaks[$category]++;
                    } else {
                        // Ketemu benar, streak putus
                        $categoryBroken[$category] = true;
                    }
                }
            }
        }

        return [
            'global_errors' => $globalConsecutiveErrors,
            'category_streaks' => $categoryStreaks
        ];
    }

    /**
     * Mendapatkan kategori dari keputusan (via content_id parsing atau DB)
     */
    public function getCategoryFromDecision($decision): ?string
    {
        if ($decision->content_id) {
            $category = $this->getCategoryFromContentId($decision->content_id);
            if ($category)
                return $category;
        }

        return null;
    }

    /**
     * Parse kategori dari Content ID string (e.g. SCN_TABUNGAN_04)
     */
    public function getCategoryFromContentId(string $contentId): ?string
    {
        $parts = explode('_', $contentId);

        // Format SCN_KATEGORI_NOMOR
        if (count($parts) >= 2) {
            $categoryCode = strtoupper($parts[1]);

            // Mapping Code to Normalized Category Key
            $mapping = [
                'PENDAPATAN' => 'pendapatan',
                'ANGGARAN' => 'anggaran',
                'TABUNGAN' => 'tabungan_dan_dana_darurat',
                'UTANG' => 'utang',
                'INVESTASI' => 'investasi',
                'ASURANSI' => 'asuransi_dan_proteksi',
                'TUJUAN' => 'tujuan_jangka_panjang'
            ];

            return $mapping[$categoryCode] ?? strtolower($categoryCode);
        }

        return null;
    }
}
