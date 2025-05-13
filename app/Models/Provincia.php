<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $table = 'provincias'; // Nombre explícito de la tabla
    protected $primaryKey = 'idProvincia'; // Clave primaria
    public $incrementing = false; // Si no es autoincrementable
    protected $keyType = 'string'; // Tipo de la clave primaria (si es string o int)
    public $timestamps = false; // Si no usas los campos created_at y updated_at

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'idDepartamento');
    }

    public function distritos()
    {
        return $this->hasMany(Distrito::class, 'idProvincia');
    }
}
