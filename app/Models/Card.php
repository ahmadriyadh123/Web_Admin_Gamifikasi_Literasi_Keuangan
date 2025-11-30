<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $table = 'cards';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Matikan timestamps jika tabel cards tidak punya updated_at/created_at yang dikelola Laravel
    // Tapi di glk_db.sql ada created_at default, jadi aman.
    public $timestamps = false; 

    protected $fillable = [
        'id', 'type', 'title', 'narration', 'scoreChange', 'action', 
        'categories', 'tags', 'difficulty', 'expected_benefit', 
        'learning_objective'
    ];

    protected $casts = [
        'categories' => 'array',
        'tags' => 'array',
        'scoreChange' => 'integer', // Di SQL Anda ini INT, bukan JSON
    ];
}