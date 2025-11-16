<?php

namespace App\Services;
use App\Services\AI\FuzzyService;
use App\Services\AI\ANNService;
use App\Services\AI\CosineSimilarityService;
use App\Models\PlayerProfile;
use App\Models\ProfilingInput;
use App\Models\ProfilingResult;

class ProfilingService
{
    public function saveOnboardingAnswers(array $input)
    {
        PlayerProfile::updateOrCreate(
            ['PlayerId' => $input['player_id']],
            [
                'onboarding_answers' => json_encode($input['answers']),
                'locale' => $input['locale'],
                'recommended_fokus' => null,
                'last_updated' => now(),
            ]
        );

        return [
            'player_id' => $input['player_id'],
            'onboarding_answers' => $input['answers'],
            'platform' => $input['platform'],
            'status' => 'recorded',
            'session_id' => $input['session_id'],
        ];
    }
}
