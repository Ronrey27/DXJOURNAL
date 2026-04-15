@extends('layout.app')
@section('title', 'login')

@section('content')

    <div class="login-card card">
        <div class="card-header-custom">
            <i class="bi bi-person-circle text-primary" style="font-size: 3rem;"></i>
            <h3 class="mt-3 fw-bold">DXJOURNAL</h3>
            <p class="text-muted">Ingresa tus credenciales para continuar</p>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label text-secondary small fw-bold">CORREO ELECTRÓNICO</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control bg-light border-start-0" value="{{ old('email') }}"
                            id="email" placeholder="ejemplo@correo.com">
                    </div>
                    <div>
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>


                </div>

                <div class="mb-4">
                    <label for="password" class="form-label text-secondary small fw-bold">CONTRASEÑA</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password"class="form-control bg-light border-start-0" id="password"
                            placeholder="••••••••">
                    </div>
                    <div>
                        @error('password')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>


                </div>

                <div class="d-flex justify-content-between mb-4 small">
                    <div class="form-check">
                        <input type="checkbox" name ="remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Recuérdame</label>
                    </div>
                    <a href="#" class="text-decoration-none text-primary">¿Olvidaste tu clave?</a>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-login text-white">
                        INICIAR SESIÓN
                    </button>
                </div>
            </form>
        </div>

        <div class="card-footer bg-light text-center py-3 border-0">
            <p class="mb-0 small text-muted">¿No tienes cuenta? <a href={{ route('register') }}
                    class="fw-bold text-decoration-none">Regístrate</a></p>
        </div>
    </div>

@endsection
