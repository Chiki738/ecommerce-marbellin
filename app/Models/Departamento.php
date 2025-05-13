<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamentos'; // Nombre explícito de la tabla
    protected $primaryKey = 'idDepartamento'; // Clave primaria
    public $incrementing = false; // Si no es autoincrementable
    protected $keyType = 'string'; // Tipo de la clave primaria (si es string o int)
    public $timestamps = false; // Si no usas los campos created_at y updated_at

    public function provincias()
    {
        return $this->hasMany(Provincia::class, 'idDepartamento');
    }
}
