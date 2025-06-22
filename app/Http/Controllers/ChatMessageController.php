<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatMessageController extends Controller
{
    public function show(Chat $chat)
    {
        // $this->authorize('view', $chat); // Comentado temporalmente si no tienes ChatPolicy
        $chat->load(['messages.user', 'users']);
        // CORRECCIÓN: Cambiado 'chats.show' a 'chat.show' para que coincida con la estructura de directorios
        return view('chat.show', compact('chat'));
    }

    public function store(Request $request, Chat $chat)
    {
        // $this->authorize('view', $chat); // Comentado temporalmente si no tienes ChatPolicy

        $request->validate([
            'contenido' => 'required|string|max:1000',
        ]);

        Message::create([
            'chat_id' => $chat->id,
            'user_id' => Auth::id(),
            'contenido' => $request->contenido,
        ]);

        return back();
    }
}
