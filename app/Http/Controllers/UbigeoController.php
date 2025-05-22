<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provincia;
use App\Models\Distrito;

class UbigeoController extends Controller
{
    // Vista con provincias para el registro
    public function signup()
    {
        $provincias = Provincia::all(['provincia_id', 'nombre']);
        return view('auth.signup', compact('provincias'));
    }

    // Retorna distritos por ID de provincia
    public function getDistritos($provincia_id)
    {
        $distritos = Distrito::where('provincia_id', $provincia_id)
            ->get(['distrito_id', 'nombre']);
        return response()->json($distritos);
    }
}
