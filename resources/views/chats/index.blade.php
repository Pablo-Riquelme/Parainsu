{{-- resources/views/chats/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Mis Mensajes')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">
                <i class="fas fa-comments"></i> Mis Mensajes
            </h1>
        </div>
        <div class="card-body">
            @if($chats->isEmpty())
                <p class="text-muted text-center">Aún no tienes conversaciones. ¡Inicia una!</p>
            @else
                <ul class="list-group list-group-flush">
                    @foreach($chats as $chat)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('chat.show', $chat->id) }}" class="text-decoration-none h5">
                                    @if($chat->es_privado)
                                        @php
                                            $otherUser = $chat->users->first(function($user) {
                                                return $user->id !== Auth::id();
                                            });
                                        @endphp
                                        @if($otherUser)
                                            <i class="fas fa-user-circle"></i> {{ $otherUser->name }}
                                        @else
                                            <i class="fas fa-question-circle"></i> Chat Privado (Usuario desconocido)
                                        @endif
                                    @else
                                        <i class="fas fa-users"></i> {{ $chat->nombre }}
                                    @endif
                                </a>
                                <div class="small text-muted">Última actividad: {{ $chat->updated_at->diffForHumans() }}</div>
                            </div>
                            <a href="{{ route('chat.show', $chat->id) }}" class="btn btn-sm btn-outline-primary">Abrir <i class="fas fa-arrow-right"></i></a>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-4 text-center">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#newChatModal">
                    <i class="fas fa-plus-circle"></i> Iniciar Nuevo Chat
                </button>
            </div>
        </div>
        <div class="card-footer text-center">
            <a href="{{ route('home') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-circle-left"></i> Volver al Menú Principal
            </a>
        </div>
    </div>
</div>

{{-- Modal para iniciar Nuevo Chat --}}
<div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newChatModalLabel">Iniciar Nuevo Chat Privado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="start-new-chat-form">
                    <div class="mb-3">
                        <label for="recipient-user" class="form-label">Selecciona un Usuario:</label>
                        <select class="form-select" id="recipient-user" required>
                            @if($otherUsers->isEmpty())
                                <option value="">No hay otros usuarios disponibles</option>
                            @else
                                <option value="">Selecciona un usuario</option>
                                @foreach($otherUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="start-chat-btn">Iniciar Chat</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Custom message display function
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
            z-index: 1050; /* Ensure it's above modal backdrops */
        `;
        document.body.appendChild(messageBox);

        setTimeout(() => {
            messageBox.style.opacity = '0';
            messageBox.addEventListener('transitionend', () => messageBox.remove());
        }, 2500); // Message visible for 2.5 seconds, then fades out

        // Add keyframes for fadeOut if not already in CSS (only once)
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

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Script de chats/index.blade.php cargado y DOM listo.');

        // Precargar el token de API para usarlo en JavaScript
        // Asegúrate de que Auth::user() no sea nulo antes de llamar a createToken
        const API_TOKEN = "{{ Auth::user() ? Auth::user()->createToken('create-chat-token')->plainTextToken : '' }}";
        if (!API_TOKEN) {
            console.error('ERROR: No se pudo generar el token de API. El usuario puede no estar autenticado o hay un problema con la generación del token.');
            showMessage('Error de autenticación: No se pudo preparar el chat.', 'danger');
            // Podrías deshabilitar el botón de chat si no hay token
            const startNewChatButton = document.querySelector('button[data-bs-target="#newChatModal"]');
            if (startNewChatButton) {
                startNewChatButton.disabled = true;
                startNewChatButton.textContent = 'Iniciar Nuevo Chat (Error)';
            }
            return; // Detener la ejecución si no hay token
        } else {
            console.log('Token de API precargado.');
        }

        const newChatModalElement = document.getElementById('newChatModal');
        const recipientSelect = document.getElementById('recipient-user');
        const startChatBtn = document.getElementById('start-chat-btn');

        if (!newChatModalElement) {
            console.error('ERROR: Elemento del modal #newChatModal no encontrado en el DOM.');
            return;
        }
        if (!recipientSelect) {
            console.error('ERROR: Elemento del select #recipient-user no encontrado en el DOM.');
            return;
        }
        if (!startChatBtn) {
            console.error('ERROR: Botón #start-chat-btn no encontrado en el DOM.');
            return;
        }

        // Función para limpiar todos los modal-backdrops y la clase 'modal-open' del body
        function cleanAllModalBackdrops() {
            console.log('Ejecutando cleanAllModalBackdrops...');
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => {
                backdrop.remove();
                console.log('modal-backdrop eliminado.');
            });
            // Asegurarse de que 'modal-open' se elimine del body
            if (document.body.classList.contains('modal-open')) {
                document.body.classList.remove('modal-open');
                console.log('Clase modal-open eliminada del body.');
            }
            document.body.style.overflow = ''; // Restaurar el scroll si fue deshabilitado
            console.log('Scroll del body restaurado.');
        }

        // Ejecutar al cargar la página para limpiar backdrops que puedan haberse quedado de una sesión anterior
        cleanAllModalBackdrops();

        // Obtener la instancia del modal de Bootstrap
        let newChatModalInstance;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            newChatModalInstance = new bootstrap.Modal(newChatModalElement);
            console.log('Instancia de Bootstrap Modal creada con éxito para #newChatModal.');
        } else {
            console.error('ERROR: Bootstrap 5 JS (window.bootstrap.Modal) NO está disponible. El modal no funcionará correctamente.');
            showMessage('Error: Bootstrap JS no cargado. El modal no funcionará.', 'danger');
            return;
        }

        // Manejar el botón "Iniciar Chat" del modal (usando fetch API)
        startChatBtn.addEventListener('click', async function () {
            console.log('Botón "Iniciar Chat" clickeado.');
            const recipientUserId = recipientSelect.value;
            if (!recipientUserId) {
                showMessage('Por favor, selecciona un usuario para iniciar el chat.', 'warning');
                console.log('No se seleccionó usuario.');
                return;
            }
            console.log('Intentando iniciar chat con usuario ID:', recipientUserId);
            showMessage('Iniciando chat...', 'info');

            try {
                console.log('Enviando solicitud POST a /api/chats/private...');
                const response = await fetch('/api/chats/private', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${API_TOKEN}`, // Usar el token precargado
                    },
                    body: JSON.stringify({ recipient_user_id: recipientUserId })
                });

                console.log('Respuesta de la API recibida. Status:', response.status);

                const result = await response.json();
                console.log('Resultado JSON de la API:', result);

                if (response.ok) {
                    showMessage(result.message || 'Chat iniciado con éxito.', 'success');
                    if (newChatModalInstance) {
                        newChatModalInstance.hide(); // Oculta el modal de Bootstrap
                    }
                    if (result.chat_id) {
                        console.log('Redirigiendo a /chat/' + result.chat_id);
                        window.location.href = `/chat/${result.chat_id}`; // Redirigir al chat
                    } else {
                        console.warn('Advertencia: chat_id no recibido en la respuesta del API.');
                        // Si no hay chat_id, al menos recargar la página para ver el nuevo chat en la lista
                        window.location.reload();
                    }
                } else {
                    console.error('Error al iniciar chat (respuesta del servidor): Status', response.status, 'Status Text:', response.statusText, 'Detalles:', result);
                    let errorMessage = 'Error al iniciar chat: ' + (result.message || 'Desconocido');
                    if (result.errors) {
                        errorMessage += '\nDetalles: ' + Object.values(result.errors).flat().join(', ');
                    }
                    showMessage(errorMessage, 'danger');
                }
            } catch (error) {
                console.error('Error de red o JavaScript al iniciar chat:', error);
                showMessage('Error de conexión o de la aplicación al iniciar chat.', 'danger');
            }
        });

        // Asegurarse de limpiar backdrops y la clase 'modal-open' cuando el modal se oculta por cualquier vía
        newChatModalElement.addEventListener('hidden.bs.modal', function () {
            console.log('Evento hidden.bs.modal disparado para #newChatModal. Limpiando backdrops.');
            cleanAllModalBackdrops(); // Ejecutar la limpieza al cerrar
        });

        // Asegurarse de limpiar backdrops también si el modal se cierra mediante el botón de cierre directo del modal
        const closeButton = newChatModalElement.querySelector('.btn-close');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                console.log('Botón de cierre del modal clickeado. Limpiando backdrops.');
                // La función cleanAllModalBackdrops se ejecutará vía 'hidden.bs.modal'
            });
        }
    });
</script>
@endpush
