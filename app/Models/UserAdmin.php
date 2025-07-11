<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAdmin extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $table = 'users_admin';

    protected $fillable = [
        'email',
        'password',
        'rol',
        'two_factor_code',           // ✅ Añadido
        'two_factor_expires_at',     // ✅ Añadido
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'two_factor_expires_at' => 'datetime', // ✅ para comparación automática
    ];
}
