<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAdmin;
use App\Models\Provincia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    Session,
    Hash,
    Log,
    Mail,
    Validator
};
use Illuminate\Auth\Events\Registered;
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

        if (Auth::guard('web')->attempt($credentials)) {
            $authUser = Auth::user();
            $user = User::find($authUser->cliente_id);

            $codigo = rand(100000, 999999);
            $user->update([
                'two_factor_code' => $codigo,
                'two_factor_expires_at' => now()->addMinutes(10),
            ]);

            Mail::to($user->email)->send(new Codigo2FAMail($codigo));

            session([
                '2fa_id' => $user->cliente_id,
                '2fa_guard' => 'web',
                '2fa_verified' => false,
            ]);

            alert()->success('Éxito', 'Código 2FA enviado a tu correo');
            return redirect()->route('2fa.verify');
        }

        if (Auth::guard('admin')->attempt($credentials)) {
            $authAdmin = Auth::guard('admin')->user();
            $admin = UserAdmin::find($authAdmin->id);

            $codigo = rand(100000, 999999);
            $admin->update([
                'two_factor_code' => $codigo,
                'two_factor_expires_at' => now()->addMinutes(10),
            ]);

            Mail::to($admin->email)->send(new Codigo2FAMail($codigo));

            session([
                '2fa_id' => $admin->id,
                '2fa_guard' => 'admin',
                '2fa_verified' => false,
            ]);

            alert()->success('Éxito', 'Código 2FA enviado a tu correo');
            return redirect()->route('2fa.verify');
        }

        alert()->error('Error', 'Correo o contraseña incorrectos.');
        return back()->withInput();
    }

    public function verify2FA(Request $request)
    {
        $request->validate(['code' => 'required']);

        $guard = session('2fa_guard');
        $id = session('2fa_id');
        $model = $guard === 'admin' ? UserAdmin::class : User::class;

        $user = $model::find($id);

        if (
            !$user ||
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
        return redirect($guard === 'admin' ? route('admin.dashboardAdmin') : route('pages.home'));
    }

    public function signup()
    {
        return view('auth.signup', ['provincias' => Provincia::all()]);
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
