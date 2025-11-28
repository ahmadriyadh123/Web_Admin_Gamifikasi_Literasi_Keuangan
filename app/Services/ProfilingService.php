<?php

namespace App\Services;

use App\Services\AI\FuzzyService;
use App\Services\AI\ANNService;
use App\Models\PlayerProfile;
use App\Models\ProfilingInput;

class ProfilingService
{
    protected $fuzzy;
    protected $ann;
    protected $cosine;

    private const CLUSTER_PROFILES = [
        'Financial Novice' => [
            'display_name' => 'HIGH RISK PLAYER',
            'level' => 'Critical',
            'traits' => ['Impulsive', 'FOMO-driven'],
            'weak_areas' => ['Utang', 'Tabungan', 'Tujuan Jangka Panjang'],
            'recommended_focus' => ['Basic Budgeting', 'Debt Management']
        ],
        'Financial Explorer' => [
            'display_name' => 'MODERATE RISK PLAYER',
            'level' => 'High',
            'traits' => ['Experimental', 'Overconfident'],
            'weak_areas' => ['Investasi', 'Anggaran'],
            'recommended_focus' => ['Risk Awareness', 'Diversification']
        ],
        'Foundation Builder' => [
            'display_name' => 'CAUTIOUS PLAYER',
            'level' => 'Medium',
            'traits' => ['Conservative', 'Saver'],
            'weak_areas' => ['Investasi', 'Pertumbuhan Aset'],
            'recommended_focus' => ['Investment Basics', 'Inflation Protection']
        ],
        'Financial Architect' => [
            'display_name' => 'STRATEGIC PLAYER',
            'level' => 'Low',
            'traits' => ['Planner', 'Optimizer'],
            'weak_areas' => ['Optimasi Pajak', 'Estate Planning'],
            'recommended_focus' => ['Advanced Portfolio', 'Tax Efficiency']
        ],
        'Financial Sage' => [
            'display_name' => 'SECURE PLAYER',
            'level' => 'Safe',
            'traits' => ['Mentor', 'Philanthropist'],
            'weak_areas' => [],
            'recommended_focus' => ['Legacy Planning', 'Wealth Transfer']
        ],
        'default' => [
            'display_name' => 'UNKNOWN PLAYER',
            'level' => 'Unknown',
            'traits' => [],
            'weak_areas' => [],
            'recommended_focus' => []
        ]
    ];
    
    public function __construct(
        FuzzyService $fuzzy,
        ANNService $ann,
    ) {
        $this->fuzzy = $fuzzy;
        $this->ann = $ann;
    }

        /**
     * Mendapatkan status profiling untuk pemain tertentu.
     */
    public function getProfilingStatus(string $playerId)
    {
        $profile = PlayerProfile::find($playerId);

        if (!$profile) {
            return [
                'player_id' => $playerId,
                'status' => 'needed',
                'profiling_done' => false,
                'cluster' => null
            ];
        }

        if (!empty($profile->cluster)) {
            return [
                'player_id' => $playerId,
                'status' => 'completed',
                'profiling_done' => true,
                'cluster' => $profile->cluster,
                'level' => $profile->level,
                'last_updated' => $profile->last_updated
            ];
        }
        
        if (!empty($profile->onboarding_answers)) {
            return [
                'player_id' => $playerId,
                'status' => 'processing',
                'profiling_done' => false,
                'message' => 'Answers received, waiting for calculation.'
            ];
        }

        return [
            'profiling_done' => false,
            'cluster' => null
        ];
    }
    
    public function saveOnboardingAnswers(array $input)
    {
        PlayerProfile::updateOrCreate(
            ['PlayerId' => $input['player_id']],
            [
                'onboarding_answers' => json_encode($input['answers']),
                'last_updated' => now(),
            ]
        );
        if (!empty($input['profiling_done']) && $input['profiling_done'] === true) {
            try {
                $this->runProfilingCluster($input['player_id']);
            } catch (\Exception $e) {
                \Log::error("Profiling calculation failed for {$input['player_id']}: " . $e->getMessage());
            }
        }
        return ['ok' => true ];
    }

    public function runProfilingCluster(string $playerId)
    {
        $input = ProfilingInput::where('player_id', $playerId)->latest()->first();
        if (!$input) {
            return ['error' => 'No profiling input found'];
        }

        $features = json_decode($input->feature, true);
        $linguisticLabels = $this->fuzzy->categorize($features);
        $finalClass = $this->ann->getFinalClass($linguisticLabels);
        $profileData = self::CLUSTER_PROFILES[$finalClass] ?? self::CLUSTER_PROFILES['default'];

        PlayerProfile::where('PlayerId', $playerId)->update([
            'cluster' => $profileData['display_name'],
            'level' => $profileData['level'],
            'traits' => json_encode($profileData['traits']),
            'weak_areas' => json_encode($profileData['weak_areas']),
            'recommended_focus' => $profileData['recommended_focus'][0] ?? null,
            'lifetime_scores' => json_encode($features),
            'last_updated' => now(),
        ]);

        return [
            'cluster' => $finalClass,
            'level' => $profileData['level'],
            'traits' => $profileData['traits'],
            'weak_areas' => $profileData['weak_areas'],
            'recommended_focus' => $profileData['recommended_focus'],
        ];
    }
}

