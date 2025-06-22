import 'bootstrap';
import '../css/app.css';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

import Pusher from 'pusher-js'; // <-- Re-importa Pusher.js (aunque uses Reverb)
window.Pusher = Pusher; // <-- Hazlo global para que el código compilado lo encuentre

window.Echo = new Echo({
    broadcaster: 'reverb', // <-- ¡Esto sigue siendo 'reverb'!
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
});

// Asegúrate de que tu modelo App\Models\User use el trait Laravel\Sanctum\HasApiTokens;
// ya que Echo utiliza Laravel Sanctum para autenticar los canales privados.
