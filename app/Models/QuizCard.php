<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizCard extends Model
{
    use HasFactory;

    protected $table = 'quiz_cards';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id', 'question', 'correctOption', 'correctScore', 'incorrectScore', 
        'tags', 'difficulty', 'learning_objective'
    ];
    protected $casts = [
        'tags' => 'array'
    ];

    public function options()
    {
        return $this->hasMany(QuizOption::class, 'quizId', 'id');
    }
}