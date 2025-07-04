<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAdmin;
use App\Models\Provincia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use App\Mail\Codigo2FAMail;

class AuthController extends Controller
{
    // Muestra la vista de login
    public function login()
    {
        return view('auth.login');
    }

    // Procesa el inicio de sesión para usuario o administrador
    public function loginPost(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Login de usuario
        if (Auth::guard('web')->attempt($credentials)) {
            $authUser = Auth::user();
            $user = User::find($authUser->cliente_id);

            // Generar código 2FA válido por 10 minutos
            $codigo = rand(100000, 999999);
            $user->two_factor_code = $codigo;
            $user->two_factor_expires_at = now()->addMinutes(10);
            $user->save();

            // Enviar el código por correo
            Mail::to($user->email)->send(new Codigo2FAMail($codigo));

            // Guardar en sesión
            session([
                '2fa_id' => $user->cliente_id,
                '2fa_guard' => 'web',
                '2fa_verified' => false,
            ]);

            return redirect()->route('2fa.verify');
        }

        // Login de administrador
        if (Auth::guard('admin')->attempt($credentials)) {
            $adminUser = Auth::guard('admin')->user();
            $admin = UserAdmin::find($adminUser->id);

            $codigo = rand(100000, 999999);
            $admin->two_factor_code = $codigo;
            $admin->two_factor_expires_at = now()->addMinutes(10);
            $admin->save();


            Mail::to($admin->email)->send(new Codigo2FAMail($codigo));

            session([
                '2fa_id' => $admin->id,
                '2fa_guard' => 'admin',
                '2fa_verified' => false,
            ]);

            return redirect()->route('2fa.verify');
        }

        return back()->with('error', 'Correo o contraseña incorrectos.')->withInput();
    }

    // Verifica el código 2FA tanto para usuarios como admins
    public function verify2FA(Request $request)
    {
        $request->validate(['code' => 'required']);

        $guard = session('2fa_guard');
        $id = session('2fa_id');

        // Obtener el modelo correcto según el tipo de usuario
        $user = $guard === 'admin'
            ? UserAdmin::find($id)
            : User::find($id);

        // Verifica si el código es correcto y no ha expirado
        if (
            !$user ||
            now()->greaterThan($user->two_factor_expires_at) ||
            $user->two_factor_code !== $request->code
        ) {
            Auth::guard($guard)->logout();
            Session::flush();
            return redirect()->route('login')->with('error', 'Código inválido o expirado. Inicia sesión nuevamente.');
        }

        // Código válido: limpiar campos 2FA
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        Auth::guard($guard)->login($user);
        session()->forget('2fa_id');
        session(['2fa_verified' => true]);

        return redirect($guard === 'admin' ? route('admin.dashboardAdmin') : route('pages.home'))->with('success', 'Autenticación 2FA exitosa.');
    }

    // Muestra el formulario de registro de usuario
    public function signup()
    {
        $provincias = Provincia::all();
        return view('auth.signup', compact('provincias'));
    }

    // Procesa el registro del usuario
    public function signupPost(Request $request)
    {
        // Validación completa (protección contra inyección SQL y datos maliciosos)
        $request->validate([
            'nombre'     => 'required|string|max:255',
            'apellido'   => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|confirmed|min:6',
            'provincia'  => 'required',
            'distrito'   => 'required',
            'direccion'  => 'required|string|max:255',
        ]);

        try {
            // Crear usuario con Eloquent (seguro contra inyección SQL)
            $user = User::create([
                'nombre'      => $request->nombre,
                'apellido'    => $request->apellido,
                'email'       => $request->email,
                'password'    => Hash::make($request->password), // ← Seguridad adicional
                'distrito_id' => $request->distrito,
                'direccion'   => $request->direccion,
            ]);

            Auth::login($user);
            event(new Registered($user));

            return redirect()->route('verification.notice')->with('success', 'Verifica tu correo antes de continuar.');
        } catch (\Exception $e) {
            Log::error('Error al registrar: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al registrar. Intenta nuevamente.');
        }
    }

    // Cierre de sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken(); // ← Previene CSRF
        return redirect('/')->with('success', 'Sesión cerrada correctamente.');
    }
}
