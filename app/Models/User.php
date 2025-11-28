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
    ];
    protected $hidden = [
        'passwordHash',
        'google_id'
    ];

    public function getAuthPassword()
    {
        return $this->passwordHash;
    }

    // 3. Definisikan Relasi ke Player (PENTING!)
    public function player()
    {
        // hasOne(ModelTujuan, FK_di_tujuan, PK_di_sini)
        return $this->hasOne(Player::class, 'user_id', 'id');
    }
}