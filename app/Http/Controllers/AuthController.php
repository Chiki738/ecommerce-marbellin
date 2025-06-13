<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
        return view('auth.signup');
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

            Auth::login($user);
            return redirect()->route('login')->with('success', 'Cuenta creada correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al registrarse. Intente nuevamente.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
