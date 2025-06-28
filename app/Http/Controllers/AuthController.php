<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Provincia;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginPost(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', 'Bienvenido como usuario');
        }

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin')->with('success', 'Bienvenido administrador');
        }

        return back()->with('error', 'Correo o contraseña incorrectos.')->onlyInput('email');
    }

    public function signup()
    {
        $provincias = Provincia::all();
        return view('auth.signup', compact('provincias'));
    }

    public function signupPost(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'provincia' => 'required',
            'distrito' => 'required',
            'direccion' => 'required|string|max:255',
        ], [
            'email.unique' => 'Este correo ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'required' => 'Este campo es obligatorio.',
        ]);

        try {
            $user = new User();
            $user->nombre = $request->nombre;
            $user->apellido = $request->apellido; 
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->distrito_id = $request->distrito;
            $user->direccion = $request->direccion;
            $user->save();

            Auth::logout();
            return redirect()->route('login')->with('success', 'Cuenta creada. Inicie sesión.');
        } catch (\Exception $e) {
            Log::error('Error al registrar usuario: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Sesión cerrada correctamente');
    }
}
