<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User; // Asegúrate de importar el modelo User
use App\Events\MessageSent; // Asegúrate de importar el evento MessageSent
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Para depuración, opcional pero útil

class ChatMessageController extends Controller
{
    public function show(Chat $chat)
    {
        // $this->authorize('view', $chat);
        $chat->load(['messages.user', 'users']);
        return view('chats.show', compact('chat'));
    }

    public function store(Request $request, Chat $chat)
    {
        // $this->authorize('view', $chat);

        $request->validate([
            'contenido' => 'required|string|max:1000',
        ]);

        $message = Message::create([ // Cambiado a $message para usarlo en el evento
            'chat_id' => $chat->id,
            'user_id' => Auth::id(),
            'contenido' => $request->contenido,
        ]);

        // Asegúrate de obtener el usuario autenticado para pasarlo al evento
        $user = Auth::user();

        // ¡¡¡NUEVA LÍNEA CRÍTICA!!! Disparar el evento de broadcasting
        event(new MessageSent($message, $user));
        Log::info('[ChatMessageController] MessageSent event dispatched for message ID: ' . $message->id); // Log para confirmación

        return back(); // Esto hará que la página se recargue si es un submit de formulario normal
    }
}
