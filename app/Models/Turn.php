<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    protected $table = 'turns';
    protected $primaryKey = 'turn_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
    public $timestamps = true;

    const UPDATED_AT = null;
    const CREATED_AT = 'started_at';

    public function player() {
        return $this->belongsTo(Player::class, 'player_id', 'PlayerId');
    }
    
    public function session() {
        return $this->belongsTo(GameSession::class, 'session_id', 'sessionId');
    }
}
