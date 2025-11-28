<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'config';
    protected $fillable = [
        'minPlayers',
        'maxPlayers',
        'max_turns',
        'version',
        'created_at',
        'updated_at'
    ];
}