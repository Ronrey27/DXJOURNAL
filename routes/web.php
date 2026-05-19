<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HabitController;
use Illuminate\Support\Facades\Route;

// --- RUTAS PÚBLICAS (Invitados) ---
Route::get('/', function () { 
    return view('welcome'); 
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// --- RUTAS PRIVADAS (Solo usuarios logueados) ---
Route::middleware('auth')->group(function () {
    
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gestión de Hábitos
    Route::post('/habits', [HabitController::class, 'store'])->name('habits.store');
    Route::post('/habits/{habit}/check', [HabitController::class, 'check'])->name('habits.check');
    Route::get('/habits/{habit}/edit', [HabitController::class, 'edit'])->name('habits.edit');
    Route::put('/habits/{habit}', [HabitController::class, 'update'])->name('habits.update');
    Route::delete('/habits/{habit}', [HabitController::class, 'destroy'])->name('habits.destroy');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
