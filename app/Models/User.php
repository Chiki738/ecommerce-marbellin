<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public $incrementing = false;
    protected $keyType = 'int'; // o 'string' si cliente_id no es entero

    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'distrito_id');
    }
}
