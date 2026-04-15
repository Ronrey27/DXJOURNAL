@extends('layout.app')
@section('title', 'Registro')

@section('content')
<div class="login-card card">
    <div class="card-header-custom">
        <i class="bi bi-person-plus-fill text-primary" style="font-size: 3rem;"></i>
        <h3 class="mt-3 fw-bold">CREAR CUENTA</h3>
        <p class="text-muted">Únete a DXJOURNAL y controla tus habitos</p>
    </div>

    <div class="card-body p-4">
        <form action="{{ route('register.post') }}" method="POST">
            @csrf

            <!-- Nombre -->
            <div class="mb-3">
                <label for="name" class="form-label text-secondary small fw-bold">NOMBRE COMPLETO</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                    <input type="text" name="name" class="form-control bg-light border-start-0 @error('name') is-invalid @enderror" 
                           id="name" placeholder="Tu nombre" value="{{ old('name') }}" required>
                </div>
                @error('name')
                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label text-secondary small fw-bold">CORREO ELECTRÓNICO</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control bg-light border-start-0 @error('email') is-invalid @enderror" 
                           id="email" placeholder="ejemplo@correo.com" value="{{ old('email') }}" required>
                </div>
                @error('email')
                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label text-secondary small fw-bold">CONTRASEÑA</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control bg-light border-start-0 @error('password') is-invalid @enderror" 
                           id="password" placeholder="••••••••" required>
                </div>
                @error('password')
                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="form-label text-secondary small fw-bold">CONFIRMAR CONTRASEÑA</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-check"></i></span>
                    <input type="password" name="password_confirmation" class="form-control bg-light border-start-0" 
                           id="password_confirmation" placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-login text-white">
                    REGISTRARSE
                </button>
            </div>
        </form>
    </div>

    <div class="card-footer bg-light text-center py-3 border-0">
        <p class="mb-0 small text-muted">¿Ya tienes cuenta? <a href="{{ route('login') }}" class="fw-bold text-decoration-none">Inicia sesión</a></p>
    </div>
</div>
@endsection
