@extends('layout.app')
@section('title', 'Dashboard')

@section('content')
    <div class="container mt-5 pb-5">
        <div class=" dashboard-content card mx-auto">

            {{-- Cabecera --}}
            <div class="card-header-custom d-flex justify-content-between align-items-center px-4">
                <div class="text-start">
                    <h3 class="fw-bold mb-0">DXJOURNAL</h3>
                    <p class="text-muted small">dashboard</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm border-2 fw-bold">
                        <i class="bi bi-box-arrow-right"></i> SALIR
                    </button>
                </form>
            </div>

            {{-- Bienvenida y Estadísticas --}}
            <div class="card-body p-5 text-center">
                <div class="mb-4">
                    <i class="bi bi-stars text-warning" style="font-size: 4rem;"></i>
                </div>
                <h2 class="fw-bold">¡{{ $saludo }}, {{ $user_name }}!</h2>
                <p class="text-secondary text-capitalize">Hoy es {{ $date }}</p>

                {{-- US: Mensajes aleatorios dinámicos --}}
                <p id="motivational-quote" class="text-muted small fst-italic px-4 mb-4">
                    <i class="bi bi-quote"></i> {{ $fraseDelDia }}
                </p>


                {{-- Barra de Progreso General --}}
                <div class="px-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-secondary">PROGRESO DEL DIARIO</span>
                        <span id="progress-text" class="small fw-bold text-primary">{{ $porcentajeProgreso }}%</span>
                    </div>

                    <div class="progress rounded-pill" style="height: 12px; background-color: #e9ecef;">
                        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success rounded-pill" role="progressbar" style="width: {{ $porcentajeProgreso }}%; transition: width 0.4s ease;" aria-valuenow="{{ $porcentajeProgreso }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>

                <div class="row mt-5 text-start">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-4 mb-3 border-start border-primary border-4">
                            <h6 class="fw-bold text-primary small">HÁBITOS</h6>
                            <span id="count-total" class="h4 fw-bold">{{ $totalHabitsCount }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-4 mb-3 border-start border-success border-4">
                            <h6 class="fw-bold text-success small">HECHOS HOY</h6>
                            <span id="count-today" class="h4 fw-bold">{{ $hechosHoyCount }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-4 mb-3 border-start border-info border-4">
                            <h6 class="fw-bold text-info small">MEJOR RACHA</h6>
                            <span class="h4 fw-bold">{{ $mejorRacha }} días</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formulario añadir --}}
            <div class="px-4 mb-4">
                <div class="card p-3 border-0 shadow-sm bg-light">
                    <form action="{{ route('habits.store') }}" method="POST" class="d-flex gap-2 habit-create-form">
                        @csrf
                        <select name="icon" class="form-select w-auto">
                            <option value="bi-droplet">💧</option>
                            <option value="bi-book">📚</option>
                            <option value="bi-bicycle">🚲</option>
                            <option value="bi-emoji-smile">😊</option>
                        </select>
                        <input type="text" name="name" class="form-control" placeholder="¿Qué hábito quieres empezar?" required>
                        <button type="submit" class="btn btn-primary px-4">AÑADIR</button>
                    </form>
                </div>
            </div>

            {{-- Listado de Hábitos --}}
            <div class="px-4 pb-4">
                <h5 class="fw-bold mb-3 text-start">Tus hábitos de hoy</h5>
                <div class="row" id="habits-wrapper">
                    @foreach($habits as $habit)
                        <div class="col-md-4 mb-3">
                            <div id="habit-{{ $habit->id }}" class="card p-3 border-0 shadow-sm text-center habit-card {{ $habit->done_today ? 'bg-success-subtle' : '' }}">
                                <div class="habit-icon-container">
                                    <div class="text-start d-flex gap-2">


                                        {{-- Boton para eliminar --}}
                                        <div class="d-inline">
                                            {{-- Solo dejamos el botón con su data-id. El formulario ya no es necesario porque JavaScript hace todo --}}
                                            <button type="button" class="btn btn-link text-muted p-0 shadow-none trigger-delete-modal" data-id="{{ $habit->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>



                                        {{-- Botón Editar --}}
                                        <button type="button" class="btn btn-link text-muted p-0 shadow-none edit-habit-btn" data-id="{{ $habit->id }}" data-name="{{ $habit->name }}" data-icon="{{ $habit->icon }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>

                                    <i class="bi {{ $habit->icon }} habit-icon {{ $habit->done_today ? 'text-success' : 'text-primary' }}"></i>

                                    <div class="habit-streak-badge">
                                        @if($habit->current_streak > 0)
                                            <span class="badge rounded-pill bg-warning text-dark">
                                                <i class="bi bi-fire"></i> {{ $habit->current_streak }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <h5 class="fw-bold mt-2 {{ $habit->done_today ? 'text-decoration-line-through text-success' : '' }}">
                                    {{ $habit->name }}
                                </h5>

                                <div class="mt-auto">
                                    <form action="{{ route('habits.check', $habit->id) }}" method="POST" class="habit-form">
                                        @csrf
                                        @if(!$habit->done_today)
                                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 w-100">Marcar hecho</button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 w-100">
                                                <i class="bi bi-check-all"></i> ¡Completado!
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Mapa de Calor --}}
            <div id="heatmap-container" class="card m-4 p-4 border-0 shadow-sm bg-light rounded-4">
                <h5 class="fw-bold mb-3 text-start">Consistencia (Últimos 28 días)</h5>
                <div class="heatmap">
                    @foreach ($heatmapDays as $day)
                        <div class="heatmap-day {{ $day['levelClass'] }}" data-bs-toggle="tooltip" title="{{ $day['tooltipTitle'] }}">
                            {{ $day['dayNumber'] }}
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <!-- Modal de Edición Asíncrono -->
    <div class="modal fade" id="editHabitModal" tabindex="-1" aria-labelledby="editHabitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold" id="editHabitModalLabel">Editar Hábito</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editHabitForm" method="POST" class="habit-update-form">
                    @csrf
                    @method('PUT')
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label text-secondary small fw-bold">NOMBRE DEL HÁBITO</label>
                            <input type="text" name="name" id="edit_name" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_icon" class="form-label text-secondary small fw-bold">ICONO REPRESENTATIVO</label>
                            <select name="icon" id="edit_icon" class="form-select bg-light border-0 py-2">
                                <option value="bi-droplet">💧 Agua</option>
                                <option value="bi-book">📚 Lectura</option>
                                <option value="bi-bicycle">🚲 Deporte</option>
                                <option value="bi-emoji-smile">😊 Bienestar</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-with="modal" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal de Confirmación de Borrado  -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-body text-center py-4">
                    <div class="text-danger mb-3">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">¿Eliminar hábito?</h5>
                    <p class="text-muted small mb-0 px-2">Esta acción es irreversible y borrará todo el historial de progreso en tu calendario.</p>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-center gap-2 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger rounded-pill px-4 btn-sm ">Sí, eliminar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Contenedor Global para Toasts (Flotante abajo a la derecha) -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <div id="liveToast" class="toast align-items-center text-white bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i id="toast-icon" class="bi bi-info-circle-fill"></i>
                    <span id="toast-message">Notificación</span>
                </div>
                <button type="button" class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>




    <script>

    </script>

@endsection