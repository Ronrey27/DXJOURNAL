@extends('layout.app') {{-- Asumiendo que tu layout se llama app.blade.php --}}

@section('title', 'Bienvenido')

@section('content')
    <div class="login-card card text-center">
        <div class="card-header-custom">
            <i class="bi bi-journal-check text-primary" style="font-size: 4rem;"></i>
            <h2 class="mt-3 fw-bold">¡Bienvenido a DXJOURNAL!</h2>
            <p class="text-muted px-3">El éxito es la suma de pequeños esfuerzos, repetidos día tras día"
                (Robert Collier)</p>
        </div>

        <div class="card-body p-4">
            <div class="d-grid gap-3">
                <a href="{{ route('login') }}" class="btn btn-primary btn-login text-white">
                    <i class="bi bi-box-arrow-in-right me-2"></i> INICIAR SESIÓN
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary border-2 fw-bold"
                    style="padding: 12px; border-radius: 8px;">
                    <i class="bi bi-person-plus me-2"></i> CREAR CUENTA
                </a>
            </div>
        </div>

        <div class="card-footer bg-light py-3 border-0">
            <small class="text-muted">Desarrollado con Laravel & Boostrap</small>
        </div>
    </div>
@endsection
