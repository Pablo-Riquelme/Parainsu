{{-- resources/views/chat/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Chat con ' . ($chat->es_privado && $chat->users->firstWhere('id', '!=', Auth::id()) ? $chat->users->firstWhere('id', '!=', Auth::id())->name : $chat->nombre))

{{-- Añade los data-attributes al body para que el JS global los use --}}
@section('body_attributes')
{{-- El data-api-token es útil si haces llamadas a APIs con Sanctum, --}}
{{-- pero no es estrictamente necesario para la autenticación del canal privado de Reverb/Echo --}}
{{-- ni para el envío de mensajes a rutas web con CSRF. Lo mantengo por si lo necesitas para otras cosas. --}}
data-current-user-id="{{ Auth::id() }}"
data-api-token="{{ Auth::user() ? Auth::user()->createToken('global-chat-token')->plainTextToken : '' }}"
@endsection

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="card-title mb-0">
                <i class="fas fa-comments"></i>
                @if($chat->es_privado)
                    @php
                        $otherUser = $chat->users->firstWhere('id', '!=', Auth::id());
                    @endphp
                    Chat con {{ $otherUser ? $otherUser->name : 'Usuario Desconocido' }}
                @else
                    {{ $chat->nombre }}
                @endif
            </h2>
            <a href="{{ route('chats.index') }}" class="btn btn-light btn-sm text-primary">
                <i class="fas fa-arrow-left"></i> Volver a Mensajes
            </a>
        </div>
        <div class="card-body">
            {{-- Input oculto para pasar el chat ID al JavaScript --}}
            <input type="hidden" id="chat-id" value="{{ $chat->id }}">

            {{-- Contenedor donde los mensajes del chat se añadirán y se mostrarán --}}
            <div class="chat-box overflow-auto p-3 mb-3 bg-light rounded" style="max-height: 400px; min-height: 200px;">
                {{-- Aquí se cargarán los mensajes --}}
                @if($chat->messages->isEmpty())
                    <p class="text-center text-muted">No hay mensajes aún en este chat. ¡Envía el primero!</p>
                @else
                    @foreach ($chat->messages as $message)
                        <div class="d-flex {{ $message->user_id == Auth::id() ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                            <div class="message-bubble {{ $message->user_id == Auth::id() ? 'bg-primary text-white' : 'bg-secondary text-white' }} rounded py-2 px-3 shadow-sm">
                                <div class="small text-opacity-75 mb-1">
                                    {{ $message->user->name }} - {{ \Carbon\Carbon::parse($message->created_at)->setTimezone('America/Santiago')->format('H:i') }} {{-- Hora de Chile --}}
                                </div>
                                {{ $message->contenido }}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="input-group">
                <input type="text" id="message-input" class="form-control" placeholder="Escribe tu mensaje...">
                <button class="btn btn-success" id="send-message-btn">
                    <i class="fas fa-paper-plane"></i> Enviar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Scripts para manejar el envío de mensajes y la recarga --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chatId = document.getElementById('chat-id').value;
    const messageInput = document.getElementById('message-input');
    const sendMessageBtn = document.getElementById('send-message-btn');
    const chatBox = document.querySelector('.chat-box'); // Para scroll al final
    const currentUserId = document.body.dataset.currentUserId; // Obtener el ID del usuario actual

    // Función para desplazarse al final del chat-box
    function scrollToBottom() {
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    }

    // Función para añadir un mensaje al chatbox
    function addMessageToChatBox(message, userName, isCurrentUser) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `d-flex ${isCurrentUser ? 'justify-content-end' : 'justify-content-start'} mb-2`;
        // Formatear la hora localmente (ej. para Chile)
        const messageTime = new Date(message.created_at).toLocaleString('es-CL', { hour: '2-digit', minute: '2-digit', timeZone: 'America/Santiago' });

        messageDiv.innerHTML = `
            <div class="message-bubble ${isCurrentUser ? 'bg-primary text-white' : 'bg-secondary text-white'} rounded py-2 px-3 shadow-sm">
                <div class="small text-opacity-75 mb-1">
                    ${userName} - ${messageTime}
                </div>
                ${message.contenido}
            </div>
        `;
        chatBox.appendChild(messageDiv);
        scrollToBottom(); // Desplazarse al final después de añadir el mensaje
    }

    // Desplazarse al final cuando la página carga
    scrollToBottom();

    // Listener para el botón de enviar mensaje
    if (sendMessageBtn) {
        sendMessageBtn.addEventListener('click', sendMessage);
    }

    // Listener para la tecla Enter en el input de mensaje
    if (messageInput) {
        messageInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Prevenir el salto de línea en el input
                sendMessage();
            }
        });
    }

    // Configuración de Laravel Echo para recibir mensajes en tiempo real
    if (typeof window.Echo !== 'undefined') {
        console.log('Echo está disponible. Suscribiéndose al canal de chat:', `private-chat.${chatId}`);
        window.Echo.private(`private-chat.${chatId}`)
            .listen('MessageSent', (e) => {
                console.log('--- EVENTO MessageSent RECIBIDO EN FRONTEND ---', e);
                // Asegurarse de que el mensaje no sea el que acabamos de enviar nosotros mismos
                // para evitar duplicados si el backend también lo añade al DOM.
                // En este caso, como lo añadimos nosotros mismos al enviar, solo queremos los de otros usuarios.
                if (e.message.user_id != currentUserId) {
                    addMessageToChatBox(e.message, e.user.name, false); // Es un mensaje de otro usuario
                }
            })
            .error((error) => {
                console.error('Error en el canal de Echo:', error);
            });
    } else {
        console.error('Laravel Echo NO está disponible. La actualización en tiempo real no funcionará.');
    }

    async function sendMessage() {
        const messageContent = messageInput.value.trim();

        if (messageContent === '') {
            return; // No enviar mensajes vacíos
        }

        // Deshabilitar input y botón para evitar envíos múltiples
        messageInput.disabled = true;
        sendMessageBtn.disabled = true;
        sendMessageBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...';

        try {
            const response = await fetch(`/chats/${chatId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ contenido: messageContent })
            });

            if (response.ok) {
                const data = await response.json();
                console.log('Mensaje enviado:', data);
                // AÑADIR EL MENSAJE AL CHATBOX INMEDIATAMENTE PARA EL REMITENTE
                // Usamos data.data porque el controlador devuelve el mensaje en 'data'
                addMessageToChatBox(data.data, 'Tú', true); // 'Tú' porque es el mensaje del usuario actual
                messageInput.value = ''; // Limpiar el input

                // NO recargar la página: window.location.reload(); // <-- ESTA LÍNEA SE ELIMINA
            } else {
                const errorData = await response.json();
                console.error('Error al enviar mensaje:', errorData);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Error al enviar mensaje: ' + (errorData.message || 'Error desconocido'), 'error');
                } else {
                    alert('Error al enviar mensaje: ' + (errorData.message || 'Error desconocido'));
                }
            }
        } catch (error) {
            console.error('Error de red al enviar mensaje:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error', 'Error de conexión. Intenta de nuevo.', 'error');
            } else {
                alert('Error de conexión. Intenta de nuevo.');
            }
        } finally {
            // Re-habilitar los campos y restaurar el botón
            messageInput.disabled = false;
            sendMessageBtn.disabled = false;
            sendMessageBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar';
        }
    }
});
</script>

{{-- El CSS se puede mantener aquí o mover a un archivo CSS externo si prefieres. --}}
<style>
    .chat-box {
        background-color: #e9ecef; /* Color de fondo suave para la caja de chat */
        border-radius: 8px;
        padding: 15px;
        max-height: 60vh; /* Altura máxima para la caja de chat */
        overflow-y: auto; /* Permite scroll si el contenido es demasiado largo */
        display: flex;
        flex-direction: column; /* Apila los mensajes verticalmente */
        gap: 10px; /* Espacio entre burbujas de mensaje */
    }
    .message-bubble {
        max-width: 75%; /* Ancho máximo de la burbuja de mensaje */
        padding: 8px 12px;
        border-radius: 15px; /* Bordes más redondeados para las burbujas */
        word-wrap: break-word; /* Rompe palabras largas */
    }
    .justify-content-end .message-bubble {
        background-color: #007bff; /* Azul para mensajes propios */
        color: white;
    }
    .justify-content-start .message-bubble {
        background-color: #6c757d; /* Gris para mensajes de otros */
        color: white;
    }
</style>
@endpush
