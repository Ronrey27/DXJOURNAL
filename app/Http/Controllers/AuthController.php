<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;
use App\Models\User; 
class AuthController extends Controller
{
    // Muestra la vista
    public function showLogin() {
        return view('auth.login');
    }

    // Procesa el inicio de sesión
    public function login(Request $request) {
        // 1. Validar los datos
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Intentar autenticar contra MySQL
        if (Auth::attempt($credentials, $request->remember)) {
            // Regenerar sesión por seguridad
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        // 3. Si falla, volver atrás con error
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }
    public function showRegister() {
    return view('auth.register');
}

    public function register(Request $request) {
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => $request->password, // El modelo User.php se encarga del hash
    ]);

    if ($user) {
        Auth::login($user);
        return redirect()->route('dashboard');
    }

    return back()->withErrors(['email' => 'Error al guardar el usuario.']);
}


public function logout(Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
}
}
