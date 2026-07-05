<?php

namespace App\Http\Controllers;

use App\Mail\Codigo2FAMail;
use App\Models\User;
use App\Models\UserAdmin;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\{Auth, Hash, Log, Mail};
use Illuminate\Validation\Rules\Password;

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
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            return $this->enviarCodigo2FA('web', Auth::guard('web')->user());
        }

        if (Auth::guard('admin')->attempt($credentials)) {
            return $this->enviarCodigo2FA('admin', Auth::guard('admin')->user());
        }

        alert()->error('Error', 'Correo o contraseña incorrectos.');
        return back()->withInput();
    }

    private function enviarCodigo2FA(string $guard, User|UserAdmin $user)
    {
        $codigo = (string) random_int(100000, 999999);

        $user->forceFill([
            'two_factor_code' => Hash::make($codigo),
            'two_factor_expires_at' => now()->addMinutes(10),
        ])->save();

        Auth::guard($guard)->logout();
        session()->regenerate();

        Mail::to($user->email)->send(new Codigo2FAMail($codigo));

        session([
            '2fa_id' => $user->getAuthIdentifier(),
            '2fa_guard' => $guard,
            '2fa_verified' => false,
        ]);

        alert()->success('Código enviado', 'Revisa tu correo para completar el acceso.');
        return redirect()->route('2fa.verify');
    }

    public function verify2FA(Request $request)
    {
        $request->validate(['code' => ['required', 'digits:6']]);

        $guard = session('2fa_guard');
        $id = session('2fa_id');

        if (!in_array($guard, ['web', 'admin'], true) || !$id) {
            return redirect()->route('login');
        }

        $user = $guard === 'admin'
            ? UserAdmin::find($id)
            : User::find($id);

        if (
            !$user ||
            !$user->two_factor_expires_at ||
            now()->greaterThan($user->two_factor_expires_at) ||
            !Hash::check($request->input('code'), (string) $user->two_factor_code)
        ) {
            Auth::guard($guard)->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            alert()->error('Error', 'Código inválido o expirado. Inicia sesión nuevamente.');
            return redirect()->route('login');
        }

        $user->forceFill([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();

        Auth::guard($guard)->login($user);
        $request->session()->regenerate();
        $request->session()->forget(['2fa_id', '2fa_guard']);
        $request->session()->put('2fa_verified', true);

        alert()->success('Autenticación exitosa', 'Bienvenido(a) al sistema');
        return redirect($guard === 'admin' ? route('admin.productosAdmin') : route('pages.home'));
    }

    public function signupPost(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'provincia' => ['required', 'exists:provincias,provincia_id'],
            'distrito' => ['required', 'exists:distritos,distrito_id'],
            'direccion' => ['required', 'string', 'max:255'],
        ]);

        try {
            $user = User::create([
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'distrito_id' => $data['distrito'],
                'direccion' => $data['direccion'],
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
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        alert()->success('Sesión cerrada', 'Has cerrado sesión correctamente.');
        return redirect('/');
    }
}
