<?php

namespace App\Services;

use App\Models\PlayerDecision;
use App\Models\ParticipatesIn;
use App\Models\InterventionTemplate;
use App\Repositories\InterventionRepository;

class FeedbackService
{
    protected $repo;

    public function __construct(InterventionRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Menyimpan respon pemain terhadap intervensi
     */
    public function storeInterventionFeedback(string $playerId, array $data)
    {
        $participation = ParticipatesIn::where('playerId', $playerId)
            ->whereHas('session', fn($q) => $q->where('status', 'active'))
            ->with('session')
            ->first();

        $sessionId = $participation ? $participation->sessionId : null;
        $turnNumber = $participation ? ($participation->session->current_turn ?? 0) : 0;

        // Catat sebagai keputusan (decision) tipe 'intervention_log'
        // Jika user mengirim teks jawaban, simpan di player_response
        PlayerDecision::create([
            'player_id' => $playerId,
            'session_id' => $sessionId,
            'turn_number' => $turnNumber,
            'content_id' => $data['scenario_id'],
            'intervention_id' => $data['intervention_id'],
            'content_type' => 'intervention_log',
            'player_response' => $data['player_response'], // Berisi ID tombol atau Teks Jawaban
            'is_correct' => false,
            'score_change' => 0,
            'created_at' => now()
        ]);

        $heedMessage = null;

        // Logic: Jika respon BUKAN 'ignored', berarti user engage (heeded) atau menjawab pertanyaan
        if ($data['player_response'] !== 'ignored') {
            $category = $this->repo->getCategoryFromContentId($data['scenario_id']);

            // Cek template Level 3
            $template = InterventionTemplate::where('level', 3)
                ->where('category', $category)
                ->first();

            if ($template && $template->heed_message) {
                $heedMessage = $template->heed_message;
            }
        }

        return [
            'ok' => true,
            'heed_message' => $heedMessage
        ];
    }
}
