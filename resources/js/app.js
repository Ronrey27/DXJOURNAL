import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {

            // Función centralizada para refrescar componentes de la página en segundo plano
            function refreshDashboardData() {
                fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        //  Sincronizamos estadísticas
                        document.getElementById('count-today').innerHTML = doc.getElementById('count-today').innerHTML;
                        document.getElementById('count-total').innerHTML = doc.getElementById('count-total').innerHTML;
                        // barra de progreso
                        document.getElementById('progress-text').innerHTML = doc.getElementById('progress-text').innerHTML;

                        const oldBar = document.getElementById('progress-bar');
                        const newBar = doc.getElementById('progress-bar');
                        if (oldBar && newBar) {
                            oldBar.style.width = newBar.style.width;
                            oldBar.setAttribute('aria-valuenow', newBar.getAttribute('aria-valuenow'));
                        }

                        //  Sincronizamos mapa de calor
                        document.getElementById('heatmap-container').innerHTML = doc.getElementById('heatmap-container').innerHTML;

                        //  Sincronizamos la lista completa de hábitos para traer la nueva tarjeta (o quitar la borrada)
                        document.getElementById('habits-wrapper').innerHTML = doc.getElementById('habits-wrapper').innerHTML;

                        // Volvemos a enlazar los eventos dinámicos a las nuevas tarjetas inyectadas
                        rebindHabitEvents();

                        // Volvemos a activar los tooltips de Bootstrap
                        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                        if (window.bootstrap && window.bootstrap.Tooltip) {
                            [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
                        }
                        document.getElementById('motivational-quote').innerHTML = doc.getElementById('motivational-quote').innerHTML;
                    });


            }

            // Encapsulamos los listeners para poder reactivarlos cuando el DOM cambie por AJAX
            function rebindHabitEvents() {

                // LOGICA: MARCAR / DESMARCAR HÁBITOS
                document.querySelectorAll('.habit-form').forEach(form => {
                    // Evitamos duplicar eventos clonando el nodo si hiciera falta, o simplemente limpiando
                    form.replaceWith(form.cloneNode(true));
                });

                document.querySelectorAll('.habit-form').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        fetch(this.action, {
                            method: 'POST',
                            body: new FormData(this),
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                            .then(response => response.json())
                            .then(data => {
                                refreshDashboardData();
                            });
                    });
                });


                // LOGICA: ELIMINAR HÁBITOS
                // Variable global temporal para recordar el ID del hábito a eliminar
                let habitIdToObtain = null;

                //  CAPTURA EL CLIC EN LA PAPELERA (Global y Dinámico)
                document.addEventListener('click', function (e) {
                    const button = e.target.closest('.trigger-delete-modal');
                    if (button) {
                        e.preventDefault();

                        // Guardamos el ID del hábito directamente desde el atributo data-id
                        habitIdToObtain = button.getAttribute('data-id');

                        // Abrimos el modal de confirmación de Bootstrap de forma nativa
                        const modalElement = document.getElementById('deleteConfirmModal');
                        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
                        modalInstance.show();
                    }
                });

                // CAPTURA EL CLIC EN "SÍ, ELIMINAR" DENTRO DEL MODAL 
                document.addEventListener('click', function (e) {
                    const confirmBtn = e.target.closest('#confirmDeleteBtn');

                    if (confirmBtn && habitIdToObtain) {
                        e.preventDefault();

                        const actionUrl = `/habits/${habitIdToObtain}`;

                        // Preparamos el token CSRF obligatorio que exige Laravel
                        const formData = new FormData();
                        formData.append('_method', 'DELETE');
                        formData.append('_token', document.querySelector('input[name="_token"]').value);

                        // Ocultamos el modal de confirmación de Bootstrap
                        const modalElement = document.getElementById('deleteConfirmModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) modalInstance.hide();

                        // Ejecutamos el envío asíncrono por Fetch
                        fetch(actionUrl, {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    // Buscamos la tarjeta física en tiempo real en la pantalla usando el ID guardado
                                    const targetCard = document.getElementById(`habit-${habitIdToObtain}`);
                                    const habitColumn = targetCard ? targetCard.closest('.col-md-4') : null;

                                    // Limpiamos la variable inmediatamente para estar listos para el siguiente hábito
                                    habitIdToObtain = null;

                                    // Animación fluida de desvanecimiento en la interfaz
                                    if (habitColumn) {
                                        habitColumn.style.transition = 'all 0.3s ease';
                                        habitColumn.style.opacity = '0';
                                        habitColumn.style.transform = 'scale(0.8)';
                                    }

                                    // Refrescamos los contadores y el mapa de calor tras la animación
                                    setTimeout(() => {
                                        refreshDashboardData();
                                        showToast('Hábito eliminado del diario.', 'danger');
                                    }, 300);
                                }
                            })
                            .catch(error => console.error('Error al eliminar:', error));
                    }
                });

            }

            // --- LOGICA: CREAR NUEVO HÁBITO ---
            document.querySelectorAll('.habit-create-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const inputTexto = this.querySelector('input[name="name"]');

                    fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                inputTexto.value = '';
                                showToast('¡Hábito creado correctamente!', 'success');
                                refreshDashboardData();
                            }
                        });
                });
            });

            // --- LOGICA EDITAR HABITO ---
            document.addEventListener('click', function (e) {

                const button = e.target.closest('.edit-habit-btn');

                if (button) {
                    e.preventDefault();

                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const icon = button.getAttribute('data-icon');

                    // Inyectamos los valores al formulario del modal
                    document.getElementById('edit_name').value = name;
                    document.getElementById('edit_icon').value = icon;
                    document.getElementById('editHabitForm').action = `/habits/${id}`;

                    // Abrimos el modal usando los atributos nativos de Bootstrap 
                    const modalElement = document.getElementById('editHabitModal');

                    // apertura del modal
                    modalElement.classList.add('show');
                    modalElement.style.display = 'block';
                    document.body.classList.add('modal-open');

                    // Creamos el fondo oscuro (backdrop) a mano
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    backdrop.id = 'custom-modal-backdrop';
                    document.body.appendChild(backdrop);

                }
            });

            // Controlar el cierre manual del modal por si acaso falló la instancia global
            document.addEventListener('click', function (e) {
                if (e.target.matches('[data-bs-dismiss="modal"]') || e.target.closest('[data-bs-dismiss="modal"]')) {
                    const modalElement = document.getElementById('editHabitModal');
                    modalElement.classList.remove('show');
                    modalElement.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    const backdrop = document.getElementById('custom-modal-backdrop');
                    if (backdrop) backdrop.remove();
                }
            });
            document.addEventListener('submit', function (e) {
                if (e.target && e.target.id === 'editHabitForm') {
                    e.preventDefault();

                    fetch(e.target.action, {
                        method: 'POST',
                        body: new FormData(e.target),
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // Forzamos a Bootstrap a cerrar el modal usando su propio botón de cancelar/cerrar
                                const modalElement = document.getElementById('editHabitModal');
                                const closeButton = modalElement.querySelector('[data-bs-dismiss="modal"]');

                                if (closeButton) {
                                    closeButton.click();
                                }

                                setTimeout(() => {
                                    refreshDashboardData();
                                }, 150);

                                showToast('¡Cambios guardados con éxito!', 'success');
                                refreshDashboardData();


                            }
                        });
                }
            });


            // Arrancamos los eventos de los botones por primera vez al cargar la página
            rebindHabitEvents();
        });
        function showToast(message, type = 'success') {
            const toastElement = document.getElementById('liveToast');
            const messageElement = document.getElementById('toast-message');
            const iconElement = document.getElementById('toast-icon');

            // Cambiamos el texto y el color de fondo según el tipo (success, danger, info)
            messageElement.textContent = message;
            toastElement.className = `toast align-items-center text-white border-0 bg-${type === 'success' ? 'success' : (type === 'danger' ? 'danger' : 'info')}`;

            // Cambiamos el icono asociándole clases de Bootstrap Icons
            iconElement.className = `bi ${type === 'success' ? 'bi-check-circle-fill' : (type === 'danger' ? 'bi-trash-fill' : 'bi-info-circle-fill')}`;

            if (window.bootstrap) {
                const toast = bootstrap.Toast.getOrCreateInstance(toastElement);
                toast.show();
            }
        }