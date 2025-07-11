<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'cliente_id';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'distrito_id',
        'direccion',
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
        'two_factor_expires_at' => 'datetime', // ✅ para comparación automática con now()
    ];

    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'distrito_id', 'distrito_id');
    }

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id', 'provincia_id');
    }
}
