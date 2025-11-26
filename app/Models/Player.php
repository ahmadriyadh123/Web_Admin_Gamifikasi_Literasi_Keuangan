<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;
    protected $table = 'players';
    protected $primaryKey = 'PlayerId';
    public $incrementing = false;
    protected $keyType = 'string';

    // Kita hanya punya 'createdAt', tidak 'updatedAt'
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;

    /**
     * Relasi (Langkah 6 di diagram): Satu Player memiliki BANYAK partisipasi.
     */
    public function participations()
    {
        return $this->hasMany(ParticipatesIn::class, 'playerId', 'PlayerId');
    }

    /**
     * Mendefinisikan relasi ke profil AI pemain.
     */
    public function profile()
    {
        return $this->hasOne(PlayerProfile::class, 'PlayerId', 'PlayerId');
    }

    public function gameSessions() {
        return $this->belongsToMany(Session::class, 'ParticipatesIn', 'playerId', 'sessionId')
                    ->withPivot('score', 'position', 'color', 'status');
    }
}
