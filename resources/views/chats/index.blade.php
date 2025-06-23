{{-- resources/views/chats/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Mis Chats')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-comments"></i> Mis Mensajes</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#newChatModal">
            <i class="fas fa-plus-circle"></i> Iniciar Nuevo Chat
        </button>
    </div>

    @forelse ($chats as $chat)
        @php
            $otherUser = $chat->users->firstWhere('id', '!=', Auth::id());
        @endphp
        <div class="card mb-2">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">
                        <i class="fas fa-user"></i> {{ $otherUser->name ?? 'Usuario desconocido' }}
                    </h5>
                    <small class="text-muted">Última actividad: {{ $chat->updated_at->diffForHumans() }}</small>
                </div>
                <a href="{{ route('chat.show', $chat->id) }}" class="btn btn-primary">
                    Abrir <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No tienes chats aún.</div>
    @endforelse

    <a href="{{ route('home') }}" class="btn btn-secondary mt-3">
        <i class="fas fa-arrow-left"></i> Volver al Menú Principal
    </a>
</div>

<!-- Modal para crear un nuevo chat -->
<div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="newChatModalLabel">Iniciar Nuevo Chat Privado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="user-select" class="form-label">Selecciona un Usuario:</label>
                    <select id="user-select" class="form-select">
                        <option value="">Selecciona un usuario</option>
                        @foreach ($otherUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="start-chat-button" class="btn btn-primary">Iniciar Chat</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('Script de chats/index.blade.php cargado y DOM listo.');

    // Función personalizada para mostrar mensajes (reemplaza alert()/Swal.fire para validaciones)
    function showMessage(message, type = 'info') {
        const messageBox = document.createElement('div');
        messageBox.textContent = message;
        messageBox.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3 shadow-lg rounded-3`;
        messageBox.style.cssText = `
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            animation: fadeOut 3s forwards;
            opacity: 1;
            transition: opacity 0.5s ease-out;
            z-index: 1050; /* Asegura que esté por encima de backdrops modales */
        `;
        document.body.appendChild(messageBox);

        setTimeout(() => {
            messageBox.style.opacity = '0';
            messageBox.addEventListener('transitionend', () => messageBox.remove());
        }, 2500);

        if (!document.getElementById('fadeOutKeyframes')) {
            const style = document.createElement('style');
            style.id = 'fadeOutKeyframes';
            style.innerHTML = `
                @keyframes fadeOut {
                    0% { opacity: 1; }
                    85% { opacity: 1; }
                    100% { opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
    }

    // Función para limpiar todos los modal-backdrops y la clase 'modal-open' del body
    function cleanAllModalBackdrops() {
        console.log('Ejecutando cleanAllModalBackdrops...');
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => {
            backdrop.remove();
            console.log('modal-backdrop eliminado.');
        });
        // Asegurarse de que 'modal-open' se elimine del body si no hay modales activos
        if (document.querySelectorAll('.modal.show').length === 0 && document.body.classList.contains('modal-open')) {
            document.body.classList.remove('modal-open');
            console.log('Clase modal-open eliminada del body.');
        }
        document.body.style.overflow = ''; // Restaurar el scroll del body
        console.log('Scroll del body restaurado.');
    }

    // *** CRÍTICO: Ejecutar al cargar la página para limpiar backdrops de sesiones anteriores ***
    cleanAllModalBackdrops();

    const newChatModalElement = document.getElementById('newChatModal');
    const userSelect = document.getElementById('user-select');
    const startChatBtn = document.getElementById('start-chat-button');

    // Validación de existencia de elementos del DOM
    if (!newChatModalElement || !userSelect || !startChatBtn) {
        console.error('ERROR: Uno o más elementos del modal no se encontraron en el DOM.');
        showMessage('Error interno: Faltan elementos de la interfaz. Recarga la página.', 'danger');
        return;
    }

    // Precargar el token de API para usarlo en JavaScript
    const API_TOKEN = "{{ Auth::user() ? Auth::user()->createToken('start-chat-token')->plainTextToken : '' }}";
    if (!API_TOKEN) {
        console.error('ERROR: No se pudo generar el token de API. El usuario puede no estar autenticado o hay un problema con la generación del token.');
        showMessage('Error de autenticación: No se pudo preparar el chat. Por favor, vuelve a iniciar sesión.', 'danger');
        startChatBtn.disabled = true; // Deshabilitar el botón si no hay token
        startChatBtn.textContent = 'Error de Autenticación';
        return;
    } else {
        console.log('Token de API precargado con éxito.');
    }

    // Obtener la instancia del modal de Bootstrap
    let newChatModalInstance;
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        newChatModalInstance = new bootstrap.Modal(newChatModalElement);
        console.log('Instancia de Bootstrap Modal creada con éxito para #newChatModal.');
    } else {
        console.error('ERROR: Bootstrap 5 JS (window.bootstrap.Modal) NO está disponible. El modal no funcionará correctamente.');
        showMessage('Error: El JavaScript de Bootstrap no se cargó correctamente. El modal no funcionará.', 'danger');
        return;
    }

    // Listener para el botón "Iniciar Chat"
    startChatBtn.addEventListener('click', async () => {
        console.log('Botón "Iniciar Chat" clickeado.');
        const selectedUserId = userSelect.value;

        if (!selectedUserId) {
            showMessage('Por favor, selecciona un usuario antes de iniciar el chat.', 'warning');
            console.log('Validación: No se seleccionó usuario.');
            return;
        }

        showMessage('Iniciando chat...', 'info');
        console.log('Intentando iniciar chat con usuario ID:', selectedUserId);

        try {
            const response = await fetch('/api/chats/private', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${API_TOKEN}` // Usar el token precargado
                },
                body: JSON.stringify({ recipient_user_id: selectedUserId })
            });

            console.log('Respuesta de la API recibida. Status:', response.status);
            const result = await response.json();
            console.log('Resultado JSON de la API:', result);

            if (response.ok && result.chat_id) {
                showMessage(result.message || 'Chat iniciado con éxito.', 'success');
                // Al escuchar 'hidden.bs.modal', la redirección se manejará después de que el modal se oculte completamente
                newChatModalElement.addEventListener('hidden.bs.modal', function redirectAfterHide() {
                    console.log('Redirigiendo a /chat/' + result.chat_id);
                    window.location.href = `/chat/${result.chat_id}`;
                    newChatModalElement.removeEventListener('hidden.bs.modal', redirectAfterHide); // Eliminar el listener después de usarlo
                }, { once: true }); // { once: true } asegura que el listener se ejecute solo una vez

                newChatModalInstance.hide(); // Oculta el modal, lo que disparará el evento 'hidden.bs.modal'
            } else {
                let errorMessage = 'Error al iniciar chat: ' + (result.message || 'Desconocido');
                if (result.errors) {
                    errorMessage += '\nDetalles: ' + Object.values(result.errors).flat().join(', ');
                }
                showMessage(errorMessage, 'danger');
                console.error('Error de API:', result);
            }
        } catch (error) {
            console.error('Error de red al iniciar chat:', error);
            showMessage('Error de conexión o de la aplicación al iniciar chat.', 'danger');
        }
    });

    // Listener para asegurar la limpieza del backdrop cuando el modal se oculta (por cualquier medio)
    newChatModalElement.addEventListener('hidden.bs.modal', function () {
        console.log('Evento hidden.bs.modal disparado para #newChatModal.');
        cleanAllModalBackdrops(); // Llamar a la función de limpieza
    });

    // Listener para cuando el modal se abre, para verificar el estado
    newChatModalElement.addEventListener('shown.bs.modal', function () {
        console.log('Modal #newChatModal ahora es visible. Verificando estado del body y backdrops.');
        // Opcional: Asegurarse de que 'modal-open' esté presente y no haya backdrops extra
        if (!document.body.classList.contains('modal-open')) {
            console.warn("La clase 'modal-open' no está en el body después de abrir el modal.");
        }
        cleanAllModalBackdrops(); // Una limpieza preventiva al mostrar también
    });
});
</script>
@endpush
