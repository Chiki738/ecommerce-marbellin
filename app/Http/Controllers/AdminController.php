<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAdmin;

class AdminController extends Controller
{
    // Mostrar lista de admins (opcional)
    public function index()
    {
        $admins = UserAdmin::all();
        return view('admin.list', compact('admins'));
    }

    // Ejemplo: Verificar si un email es admin
    public function isAdmin($email)
    {
        return UserAdmin::where('email', $email)->exists();
    }

    // Ruta protegida para admin (ejemplo)
    public function dashboard()
    {
        return view('admin.appAdmin');
    }
}
