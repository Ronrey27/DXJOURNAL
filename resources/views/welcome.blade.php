@extends('layout.app')

@section('title', 'Bienvenido')

@section('content')
    <div class="login-card card text-center">
        <div class="card-header-custom">
            <i class="bi bi-journal-check text-primary welcome-icon"></i>
            <h2 class="mt-3 fw-bold">¡Bienvenido a DXJOURNAL!</h2>
            <p class="text-muted px-3">
                "El éxito es la suma de pequeños esfuerzos, repetidos día tras día" (Robert Collier)
            </p>
        </div>

        <div class="card-body p-4">
            <div class="d-grid gap-3">
                @auth
                    {{-- Usuario Logueado --}}
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-login text-white">
                        <i class="bi bi-speedometer2 me-2"></i> IR AL DASHBOARD
                    </a>
                    
                    <form action="{{ route('logout') }}" method="POST" class="d-grid">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-logout">
                            <i class="bi bi-box-arrow-right me-2"></i> CERRAR SESIÓN
                        </button>
                    </form>
                @else
                    {{-- Invitados --}}
                    <a href="{{ route('login') }}" class="btn btn-primary btn-login text-white">
                        <i class="bi bi-box-arrow-in-right me-2"></i> INICIAR SESIÓN
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-register">
                        <i class="bi bi-person-plus me-2"></i> CREAR CUENTA
                    </a>
                @endauth
            </div>
        </div>

        <div class="card-footer bg-light py-3 border-0">
            <small class="text-muted">Desarrollado con Laravel & Bootstrap</small>
        </div>
    </div>
@endsection
