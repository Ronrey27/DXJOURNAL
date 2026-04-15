<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

//Rutas register 
// Mostrar formulario de registro
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

// Procesar el registro
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Dashboard protegido por el middleware 'auth'
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Ruta para Cerrar Sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use Illuminate\Support\Facades\DB;

Route::get('/force-save', function () {
    $user = \App\Models\User::create([
        'name' => 'Usuario de Prueba',
        'email' => 'prueba' . rand(1, 999) . '@test.com',
        'password' => '12345678'
    ]);

    return "🚀 Usuario creado con ID: " . $user->id . ". ¡Revisa ahora tu cliente SQL!";
});
Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return "✅ Conexión exitosa a la base de datos: " . DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        return "❌ Error de conexión: " . $e->getMessage();
    }
});


