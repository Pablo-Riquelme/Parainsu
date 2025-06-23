    {{-- resources/views/chat/show.blade.php --}}
    @extends('layouts.app')

    @section('title', 'Chat con ' . ($chat->es_privado && $chat->users->firstWhere('id', '!=', Auth::id()) ? $chat->users->firstWhere('id', '!=', Auth::id())->name : $chat->nombre))

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
                <div class="chat-box overflow-auto p-3 mb-3 bg-light rounded" style="max-height: 400px; min-height: 200px;">
                    {{-- Aquí se cargarán los mensajes --}}
                    @if($chat->messages->isEmpty())
                        <p class="text-center text-muted">No hay mensajes aún en este chat. ¡Envía el primero!</p>
                    @else
                        @foreach ($chat->messages as $message)
                            <div class="d-flex {{ $message->user_id == Auth::id() ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                                <div class="message-bubble {{ $message->user_id == Auth::id() ? 'bg-primary text-white' : 'bg-secondary text-white' }} rounded py-2 px-3 shadow-sm">
                                    <div class="small text-opacity-75 mb-1">
                                        {{ $message->user->name }} - {{ $message->created_at->format('H:i') }}
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
    <script>
        // JS para la funcionalidad de chat en tiempo real
        // Aquí iría la lógica para enviar y recibir mensajes a través de la API y WebSockets (Reverb)
        document.addEventListener('DOMContentLoaded', function() {
            const chatId = {{ $chat->id }}; // Obtén el ID del chat de Blade
            const messageInput = document.getElementById('message-input');
            const sendMessageBtn = document.getElementById('send-message-btn');
            const chatBox = document.querySelector('.chat-box');

            // Función para añadir un mensaje al DOM
            function addMessageToChatBox(message, userName, isCurrentUser) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `d-flex ${isCurrentUser ? 'justify-content-end' : 'justify-content-start'} mb-2`;
                messageDiv.innerHTML = `
                    <div class="message-bubble ${isCurrentUser ? 'bg-primary text-white' : 'bg-secondary text-white'} rounded py-2 px-3 shadow-sm">
                        <div class="small text-opacity-75 mb-1">
                            ${userName} - ${new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                        </div>
                        ${message.contenido}
                    </div>
                `;
                chatBox.appendChild(messageDiv);
                chatBox.scrollTop = chatBox.scrollHeight; // Scroll al final
            }

            // Aquí iría la lógica para cargar mensajes antiguos si es necesario
            // Y para escuchar nuevos mensajes vía Reverb (WebSockets)
            // Por ejemplo:
            // Echo.private(`chat.${chatId}`)
            //     .listen('MessageSent', (e) => {
            //         console.log('Mensaje recibido vía WebSocket:', e.message);
            //         addMessageToChatBox(e.message, e.user.name, e.message.user_id === {{ Auth::id() }});
            //     });

            // Lógica para enviar mensaje (usando la API que ya creamos)
            sendMessageBtn.addEventListener('click', async () => {
                const messageContent = messageInput.value.trim();
                if (messageContent === '') {
                    return;
                }

                // Generar token al momento de enviar (o usar uno precargado si está disponible)
                // Usaremos un token precargado para simplificar, como en chats/index.blade.php
                const API_TOKEN = "{{ Auth::user() ? Auth::user()->createToken('send-message-token')->plainTextToken : '' }}";
                if (!API_TOKEN) {
                    console.error('Error: Token de autenticación no disponible.');
                    // showMessage('No se pudo enviar el mensaje: error de autenticación.', 'danger');
                    return;
                }

                try {
                    const response = await fetch(`/api/chats/${chatId}/messages`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${API_TOKEN}`
                        },
                        body: JSON.stringify({ contenido: messageContent })
                    });

                    const result = await response.json();

                    if (response.ok) {
                        console.log('Mensaje enviado exitosamente:', result.data);
                        // No necesitas añadir el mensaje aquí si usas WebSockets,
                        // ya que el evento MessageSent lo hará.
                        // Si no usas WebSockets, descomenta la siguiente línea:
                        // addMessageToChatBox(result.data, "{{ Auth::user()->name }}", true);
                        messageInput.value = ''; // Limpiar el input

                    } else {
                        console.error('Error al enviar mensaje:', result);
                        // showMessage(result.message || 'Error al enviar mensaje.', 'danger');
                    }
                } catch (error) {
                    console.error('Error de red al enviar mensaje:', error);
                    // showMessage('Error de conexión al enviar mensaje.', 'danger');
                }
            });

            // Permite enviar mensaje con Enter
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessageBtn.click();
                }
            });
        });
    </script>
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
    