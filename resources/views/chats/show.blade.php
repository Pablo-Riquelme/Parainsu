{{-- resources/views/chat/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Chat: ' . ($chat->nombre ?? 'Conversación Privada'))

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">
                Chat:
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
            </h1>
        </div>
        <div class="card-body">
            <div id="chat-app" data-chat-id="{{ $chat->id }}">
                <div id="messages-container" class="border p-3 mb-3 bg-light rounded" style="height: 400px; overflow-y: auto; display: flex; flex-direction: column-reverse;">
                    {{-- Messages will be loaded and added here --}}
                </div>

                <div class="input-group">
                    <input type="text" id="message-input" class="form-control" placeholder="Escribe tu mensaje...">
                    <button class="btn btn-primary" id="send-button">Enviar</button>
                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <a href="{{ route('chats.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-circle-left"></i> Volver a Mis Mensajes
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatApp = document.getElementById('chat-app');
        if (!chatApp) return; // Exit if the chat element does not exist

        const chatId = chatApp.dataset.chatId;
        const messagesContainer = document.getElementById('messages-container');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-button');

        // Function to add a message to the container
        function addMessageToContainer(messageContent, senderName, isCurrentUser) {
            const messageElement = document.createElement('div');
            messageElement.classList.add('message', isCurrentUser ? 'text-end' : 'text-start', 'mb-2');
            messageElement.innerHTML = `
                <small class="text-muted">${senderName}</small><br>
                <span class="badge ${isCurrentUser ? 'bg-primary' : 'bg-secondary'} p-2 rounded-3">${messageContent}</span>
            `;
            messagesContainer.prepend(messageElement); // Add to the beginning so that scrolling works in reverse
        }

        // Load existing messages (when the page starts)
        async function loadMessages() {
            try {
                const response = await fetch(`/api/chats/${chatId}/messages`, {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        // The Sanctum token to authenticate the API request
                        'Authorization': `Bearer {{ Auth::user()->createToken('chat-messages-token')->plainTextToken }}`,
                    }
                });
                const data = await response.json();
                messagesContainer.innerHTML = ''; // Clear before loading new messages

                // Messages may come paginated, and in reverse chronological order (latest()).
                // We iterate them in reverse order to display them from bottom to top in the container.
                if (data.data && Array.isArray(data.data)) {
                    data.data.reverse().forEach(msg => {
                        const isCurrentUser = msg.user_id === {{ Auth::id() }};
                        addMessageToContainer(msg.contenido, msg.user.name, isCurrentUser);
                    });
                }
                messagesContainer.scrollTop = messagesContainer.scrollHeight; // Scroll to the bottom to see the latest messages
            } catch (error) {
                console.error('Error loading messages:', error);
                messagesContainer.innerHTML = '<p class="text-danger text-center">Error al cargar mensajes. Recarga la página.</p>';
            }
        }

        // Function to send a message
        sendButton.addEventListener('click', async function () {
            const contenido = messageInput.value.trim();
            if (contenido === '') return; // Do not send empty messages

            try {
                const response = await fetch(`/api/chats/${chatId}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        // The Sanctum token to authenticate the API request
                        'Authorization': `Bearer {{ Auth::user()->createToken('send-message-token')->plainTextToken }}`,
                    },
                    body: JSON.stringify({ contenido: contenido })
                });
                const result = await response.json();

                if (response.ok) {
                    console.log('Message sent via API:', result.data);
                    // Add the message directly to the chat container of the sender
                    // (the WebSocket will take care of sending it to others)
                    addMessageToContainer(result.data.contenido, result.data.user.name, true);
                    messageInput.value = ''; // Clear the text field
                    messagesContainer.scrollTop = messagesContainer.scrollHeight; // Scroll to the bottom
                } else {
                    console.error('Error sending message:', result);
                    alert('Error al enviar mensaje: ' + (result.message || 'Desconocido'));
                }
            } catch (error) {
                console.error('Network error sending message:', error);
                alert('Error de red al enviar mensaje.');
            }
        });

        // Allow sending the message by pressing Enter in the text field
        messageInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                sendButton.click();
            }
        });

        // Configure Laravel Echo to listen to the private chat channel
        // Ensure that window.Echo is available (configured in bootstrap.js)
        if (window.Echo) {
            window.Echo.private(`chat.${chatId}`)
                .listen('MessageSent', (e) => {
                    // This code runs when a message is received via WebSocket
                    console.log('WebSocket message received:', e.message.contenido);
                    const isCurrentUser = e.user.id === {{ Auth::id() }};
                    addMessageToContainer(e.message.contenido, e.user.name, isCurrentUser);
                    messagesContainer.scrollTop = messagesContainer.scrollHeight; // Scroll to the bottom
                })
                .error((error) => {
                    console.error('Error listening to chat channel:', error);
                    alert('Error de conexión con el chat en tiempo real. Recarga la página.');
                });
        } else {
            console.warn('Laravel Echo is not available. Real-time chat will not work.');
        }

        // Load messages when the page loads
        loadMessages();
    });
</script>
@endpush
