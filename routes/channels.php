<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Chat; // ¡Asegúrate de que este modelo exista y esté en la ruta correcta!
use Illuminate\Support\Facades\Log; // Importa la clase Log para depuración

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an an authenticated user can listen to the channel.
|
*/

// Canal privado para el chat
// ¡¡¡ESTE NOMBRE DEL CANAL ES CRÍTICO!!!
// Si tu evento usa PrivateChannel('chat.X'), el canal AUTORIZADO aquí DEBE ser 'private-chat.{chatId}'
Broadcast::channel('private-chat.{chatId}', function ($user, $chatId) { // <-- ¡CAMBIO AQUÍ!
    // Registra información útil para depuración en storage/logs/laravel.log
    Log::info('--- Intentando autenticar canal de chat (private-chat) ---'); // Log actualizado
    Log::info('Usuario autenticado ($user):', [
        'user_id' => $user ? $user->id : 'null',
        'user_name' => $user ? $user->name : 'null'
    ]);
    Log::info('ID del chat recibido ($chatId):', ['chat_id_param' => $chatId]);

    // 1. Verificar si hay un usuario autenticado. Si no, denegar acceso.
    if (!$user) {
        Log::warning('Autenticación de canal fallida: Usuario no autenticado.');
        return false;
    }

    // 2. Buscar el chat en la base de datos usando el ID proporcionado.
    $chat = Chat::find($chatId);

    // Registra si el chat fue encontrado o no
    Log::info('Chat encontrado ($chat):', ['chat_found' => $chat ? $chat->id : 'null']);

    // 3. Si el chat no existe, denegar el acceso.
    if (!$chat) {
        Log::warning('Autenticación de canal fallida: Chat no encontrado para ID: ' . $chatId);
        return false;
    }

    // 4. Verificar si el usuario autenticado es un participante de este chat.
    $isParticipant = $chat->users->contains($user->id);

    // Registra el resultado de la verificación de participación
    Log::info('Verificando si usuario es participante:', [
        'user_id' => $user->id,
        'chat_id' => $chat->id,
        'is_participant' => $isParticipant ? 'true' : 'false'
    ]);

    // Si el usuario no es participante, registra una advertencia.
    if (!$isParticipant) {
        Log::warning('Autenticación de canal fallida: Usuario no es participante del chat ' . $chat->id);
    }

    // Devuelve true si el usuario es participante, false en caso contrario.
    return $isParticipant;
});

// Canal de usuario (usado comúnmente para notificaciones directas a un usuario específico)
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

