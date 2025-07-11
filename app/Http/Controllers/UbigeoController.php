<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use App\Models\Distrito;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class UbigeoController extends Controller
{
    // Muestra vista con provincias para el registro
    public function signup(): View
    {
        return view('auth.signup', [
            'provincias' => Provincia::all(['provincia_id', 'nombre'])
        ]);
    }

    // Retorna distritos según ID de provincia
    public function getDistritos(int $provincia_id): JsonResponse
    {
        $distritos = Distrito::where('provincia_id', $provincia_id)
            ->get(['distrito_id', 'nombre']);

        return response()->json($distritos);
    }
}
