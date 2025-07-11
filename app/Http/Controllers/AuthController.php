<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAdmin;
use App\Models\Provincia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Session, Hash, Log, Mail, Validator};
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Carbon;
use App\Mail\Codigo2FAMail;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginPost(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Usuario
        if (Auth::guard('web')->attempt($credentials)) {
            $user = User::find(Auth::user()->cliente_id);
            return $this->enviarCodigo2FA('web', $user);
        }

        // Admin
        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = UserAdmin::find(Auth::guard('admin')->user()->id);
            return $this->enviarCodigo2FA('admin', $admin);
        }

        alert()->error('Error', 'Correo o contraseña incorrectos.');
        return back()->withInput();
    }

    private function enviarCodigo2FA(string $guard, $user)
    {
        $codigo = rand(100000, 999999);

        $user->update([
            'two_factor_code' => $codigo,
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new Codigo2FAMail($codigo));

        session([
            '2fa_id' => $user->id ?? $user->cliente_id,
            '2fa_guard' => $guard,
            '2fa_verified' => false,
        ]);

        alert()->success('Éxito', 'Código 2FA enviado a tu correo');
        return redirect()->route('2fa.verify');
    }

    public function verify2FA(Request $request)
    {
        $request->validate(['code' => 'required']);

        $guard = session('2fa_guard');
        $id = session('2fa_id');

        $user = $guard === 'admin'
            ? UserAdmin::find($id)
            : User::find($id);

        if (
            !$user ||
            !$user->two_factor_expires_at || // 👈 evita error si es null
            now()->greaterThan($user->two_factor_expires_at) ||
            $user->two_factor_code !== $request->code
        ) {
            Auth::guard($guard)->logout();
            Session::flush();
            alert()->error('Error', 'Código inválido o expirado. Inicia sesión nuevamente.');
            return redirect()->route('login');
        }

        $user->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);

        Auth::guard($guard)->login($user);
        session()->forget('2fa_id');
        session(['2fa_verified' => true]);

        alert()->success('Autenticación exitosa', 'Bienvenido(a) al sistema');
        return redirect($guard === 'admin' ? route('admin.productosAdmin') : route('pages.home'));
    }

    public function signup()
    {
        $provincias = Provincia::all();
        return view('auth.signup', compact('provincias'));
    }

    public function signupPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'     => 'required|string|max:255',
            'apellido'   => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|confirmed|min:6',
            'provincia'  => 'required',
            'distrito'   => 'required',
            'direccion'  => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = User::create([
                'nombre'      => $request->nombre,
                'apellido'    => $request->apellido,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'distrito_id' => $request->distrito,
                'direccion'   => $request->direccion,
            ]);

            Auth::login($user);
            event(new Registered($user));

            return response()->json(['redirect' => route('verification.notice')]);
        } catch (\Exception $e) {
            Log::error('Error al registrar: ' . $e->getMessage());
            return response()->json(['errors' => ['general' => ['Error inesperado. Intenta nuevamente.']]], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        alert()->success('Sesión cerrada', 'Has cerrado sesión correctamente.');
        return redirect('/');
    }
}
