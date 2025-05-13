<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Provincia;
use Illuminate\Http\Request;

class UbigeoController extends Controller
{
    public function departamentos()
    {
        // Obtener todos los departamentos
        $departamentos = Departamento::all();

        // Retornar los departamentos como JSON, solo con el nombre y el id
        return response()->json($departamentos->map(function ($departamento) {
            return [
                'idDepartamento' => $departamento->idDepartamento,
                'nombre' => $departamento->nombre
            ];
        }));
    }

    public function provincias($idDepartamento)
    {
        // Obtener provincias del departamento seleccionado
        $provincias = Departamento::findOrFail($idDepartamento)->provincias;

        // Retornar las provincias como JSON, solo con el nombre y el id
        return response()->json($provincias->map(function ($provincia) {
            return [
                'idProvincia' => $provincia->idProvincia,
                'nombre' => $provincia->nombre
            ];
        }));
    }

    public function distritos($idProvincia)
    {
        // Obtener distritos de la provincia seleccionada
        $distritos = Provincia::findOrFail($idProvincia)->distritos;

        // Retornar los distritos como JSON, solo con el nombre y el id
        return response()->json($distritos->map(function ($distrito) {
            return [
                'idDistrito' => $distrito->idDistrito,
                'nombre' => $distrito->nombre
            ];
        }));
    }
}
