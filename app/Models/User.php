<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'auth_users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'username',
        'google_id',
        'passwordHash',
        'role',
        'avatar',
        'is_active',
        'ban_reason'
    ];
    protected $hidden = [
        'passwordHash',
        'google_id',
        'ban_reason'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getAuthPassword()
    {
        return $this->passwordHash;
    }

    public function player()
    {
        return $this->hasOne(Player::class, 'user_id', 'id');
    }
}