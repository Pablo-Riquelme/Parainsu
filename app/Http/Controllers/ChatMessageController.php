<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => Auth::id(),
            'contenido' => $request->contenido,
        ]);

        $user = Auth::user();

        // Disparar el evento de broadcasting (si lo tienes configurado para tiempo real)
        event(new MessageSent($message, $user));
        Log::info('[ChatMessageController] MessageSent event dispatched for message ID: ' . $message->id);

        return response()->json([
            'message' => 'Mensaje enviado con éxito',
            'data' => $message,
            'ok' => true 
        ], 200);
    }
}
