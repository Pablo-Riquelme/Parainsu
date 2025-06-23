// resources/js/app.js

import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    disableStats: true,
    encrypted: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});

console.log('Laravel Echo configurado.');

window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('Echo conectado a Reverb!');
});
window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('Echo desconectado de Reverb.');
});
window.Echo.connector.pusher.connection.bind('error', (err) => {
    console.error('Error de conexión Echo:', err);
});

document.addEventListener('DOMContentLoaded', function() {
    const chatBox = document.querySelector('.chat-box');
    const messageInput = document.getElementById('message-input');
    const sendMessageBtn = document.getElementById('send-message-btn');
    const currentUserId = document.body.dataset.currentUserId;
    const chatIdElement = document.getElementById('chat-id');

    if (!chatBox || !messageInput || !sendMessageBtn || !currentUserId || !chatIdElement) {
        console.log('Elementos de chat no encontrados en esta página. No se activará la escucha de Echo para chat.');
        return;
    }

    const chatId = chatIdElement.value;

    console.log('Elementos de chat encontrados. Inicializando listeners para chat ID:', chatId);

    function addMessageToChatBox(message, userName, isCurrentUser) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `d-flex ${isCurrentUser ? 'justify-content-end' : 'justify-content-start'} mb-2`;
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
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    if (typeof window.Echo !== 'undefined') {
        // --- ¡¡¡ESTA ES LA LÍNEA CRÍTICA A CAMBIAR!!! ---
        console.log('Echo está disponible. Suscribiéndose al canal de chat:', `private-chat.${chatId}`); // Log actualizado
        window.Echo.private(`private-chat.${chatId}`) // <--- ¡CAMBIADO DE 'chat.${chatId}' a 'private-chat.${chatId}'!
            .listen('MessageSent', (e) => {
                console.log('--- EVENTO MessageSent RECIBIDO EN FRONTEND ---', e); // <-- Nuevo log explícito
                addMessageToChatBox(e.message, e.user.name, e.message.user_id == currentUserId);
            })
            .error((error) => {
                console.error('Error en el canal de Echo:', error);
            });
    } else {
        console.error('Laravel Echo NO está disponible. La actualización en tiempo real no funcionará.');
    }

    sendMessageBtn.addEventListener('click', async () => {
        const messageContent = messageInput.value.trim();
        if (messageContent === '') {
            return;
        }

        const API_TOKEN = document.body.dataset.apiToken;

        if (!API_TOKEN) {
            console.error('Error: Token de autenticación no disponible para enviar mensaje.');
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
                console.log('Mensaje enviado exitosamente a la API:', result.data);
                messageInput.value = '';
            } else {
                console.error('Error al enviar mensaje a la API:', result);
            }
        } catch (error) {
            console.error('Error de red al enviar mensaje:', error);
        }
    });

    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessageBtn.click();
        }
    });
});