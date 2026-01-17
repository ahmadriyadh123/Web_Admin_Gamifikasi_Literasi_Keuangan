<?php

namespace App\Services;

use App\Models\InterventionTemplate;
use App\Repositories\InterventionRepository;
use Illuminate\Support\Str;

class InterventionService
{
    protected $repository;

    public function __construct(InterventionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Cek apakah intervensi perlu ditrigger berdasarkan performa player
     *
     * @return array|null
     */
    public function checkInterventionTrigger(string $playerId): ?array
    {
        // 1. Ambil data keputusan (limit 15 untuk analisis streak mendalam)
        $decisions = $this->repository->getRecentDecisions($playerId, 15);

        // 2. Analisis kesalahan via Repository
        $analysis = $this->repository->analyzeStreaks($decisions);

        $consecutiveErrors = $analysis['global_errors'];
        $categoryStreaks = $analysis['category_streaks'];

        if ($consecutiveErrors < 2 && empty($categoryStreaks)) {
            return null;
        }

        // 3. Tentukan Level Intervensi
        $triggerLevel = 0;
        $targetCategory = null;

        // Prioritas 1: Level 3 (Blocking) - 4x salah di kategori yang sama
        foreach ($categoryStreaks as $cat => $streak) {
            if ($streak >= 4) {
                $triggerLevel = 3;
                $targetCategory = $cat;
                break;
            }
        }

        // Prioritas 2: Level 2 (Warning) - 3x salah di kategori yang sama (jika belum level 3)
        if ($triggerLevel === 0) {
            foreach ($categoryStreaks as $cat => $streak) {
                if ($streak >= 3) {
                    $triggerLevel = 2;
                    $targetCategory = $cat;
                    break;
                }
            }
        }

        // Prioritas 3: Level 1 (Reminder) - 2x salah global + Probabilitas
        if ($triggerLevel === 0 && $consecutiveErrors >= 2) {
            // Probabilitas 60%
            if (rand(1, 100) <= 60) {
                $triggerLevel = 1;
            }
        }

        if ($triggerLevel === 0) {
            return null;
        }

        // 4. Ambil Template
        $query = InterventionTemplate::where('level', $triggerLevel);

        if ($targetCategory) {
            $query->where('category', $targetCategory);
        } else {
            $query->whereNull('category'); // General template
        }

        $template = $query->inRandomOrder()->first();

        // Fallback ke general jika spesifik tidak ada
        if (!$template && $targetCategory) {
            $template = InterventionTemplate::where('level', $triggerLevel)
                ->whereNull('category')
                ->inRandomOrder()
                ->first();
        }

        if (!$template) {
            return null;
        }

        // 5. Format Response
        $message = $template->message_template;
        $title = $template->title_template;

        // Replace placeholders if any
        if ($targetCategory) {
            $catName = ucwords(str_replace(['_'], [' '], $targetCategory));
            $message = str_replace('{category}', $catName, $message);
        }

        return [
            'intervention_id' => 'intv_' . Str::random(8),
            'intervention_level' => $template->level,
            'title' => $title,
            'message' => $message,
            'is_mandatory' => (bool) $template->is_mandatory,
            'options' => $template->actions_template,
            'heed_message' => $template->heed_message
        ];
    }
}