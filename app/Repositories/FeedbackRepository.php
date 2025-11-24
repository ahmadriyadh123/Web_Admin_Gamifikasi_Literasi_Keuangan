<?php

namespace App\Repositories;

use App\Models\Telemetry;

class FeedbackRepository
{
    // Mencatat log feedback ke tabel telemetry
    public function logIntervention(array $data)
    {
    return Telemetry::create([
                'playerId'   => $data['player_id'],
                'sessionId'  => $data['session_context']['session_id'] ?? null,
                
                'turn_id'    => $data['session_context']['turn_id'] ?? 0, 
                'tile_id'    => $data['session_context']['tile_id'] ?? 0,

                'action'     => 'intervention_feedback',
                'details'    => $data,
                'metadata'   => [], 
                'created_at' => now()
            ]);
        }
}