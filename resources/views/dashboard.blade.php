@extends('layout.app')
@section('title', 'Dashboard')

@section('content')
<div class="container mt-5">
    <div class="login-card card mx-auto" style="max-width: 800px;">
        <div class="card-header-custom d-flex justify-content-between align-items-center px-4">
            <div class="text-start">
                <h3 class="fw-bold mb-0">DXJOURNAL</h3>
                <p class="text-muted small">Panel de Control</p>
            </div>
            <!-- Botón de Cerrar Sesión -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm border-2 fw-bold">
                    <i class="bi bi-box-arrow-right"></i> SALIR
                </button>
            </form>
        </div>

        <div class="card-body p-5 text-center">
            <div class="mb-4">
                <i class="bi bi-speedometer2 text-primary" style="font-size: 4rem;"></i>
            </div>
            <h2 class="fw-bold">¡Hola, {{ Auth::user()->name }}!</h2>
            <p class="text-secondary">Has iniciado sesión correctamente en tu diario de proyectos.</p>
            
            <div class="row mt-5 text-start">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-4 mb-3">
                        <h6 class="fw-bold text-primary">PROYECTOS</h6>
                        <span class="h4 fw-bold">0</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-4 mb-3">
                        <h6 class="fw-bold text-primary">TAREAS</h6>
                        <span class="h4 fw-bold">0</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-4 mb-3">
                        <h6 class="fw-bold text-primary">NOTAS</h6>
                        <span class="h4 fw-bold">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
