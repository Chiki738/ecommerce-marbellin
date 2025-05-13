<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $table = 'distritos'; // Nombre explícito de la tabla
    protected $primaryKey = 'idDistrito'; // Clave primaria
    public $incrementing = false; // Si no es autoincrementable
    protected $keyType = 'string'; // Tipo de la clave primaria (si es string o int)
    public $timestamps = false; // Si no usas los campos created_at y updated_at

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'idProvincia');
    }
}
