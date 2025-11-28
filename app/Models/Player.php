<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $table = 'players';
    protected $primaryKey = 'PlayerId';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'PlayerId',
        'user_id',
        'name',
        'avatar_url',
        'gamesPlayed',
        'initial_platform',
        'locale',
        'updated_at',
        'created_at'
    ];
}
