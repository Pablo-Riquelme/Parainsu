<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use App\Models\Message; // Asegúrate de que esta importación sea necesaria si la usas aquí
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Carga los chats del usuario, con sus usuarios relacionados
        $chats = $user->chats()->with('users')->orderByDesc('updated_at')->get();
        // Obtiene otros usuarios para el modal, excluyendo al actual
        $otherUsers = User::where('id', '!=', $user->id)->orderBy('name')->get();

        return view('chats.index', compact('chats', 'otherUsers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'destinatario_id' => 'required|exists:users,id',
            'mensaje' => 'required|string|max:1000', // Validar el mensaje inicial
        ]);

        $usuarioActual = Auth::user();
        $destinatarioId = $request->destinatario_id;
        $mensajeInicial = $request->mensaje;

        // Revisa si ya existe un chat privado entre estos 2 usuarios
        // Mejorar la búsqueda de chat privado para que sea más robusta
        $chatExistente = Chat::where('es_privado', true)
            ->whereHas('users', function ($q) use ($usuarioActual) {
                $q->where('user_id', $usuarioActual->id);
            })
            ->whereHas('users', function ($q) use ($destinatarioId) {
                $q->where('user_id', $destinatarioId);
            })
            ->withCount('users') // Contar los usuarios del chat
            ->get()
            ->filter(function ($chat) use ($usuarioActual, $destinatarioId) {
                // Filtrar para asegurarse de que el chat tiene exactamente 2 usuarios: el actual y el destinatario
                return $chat->users_count === 2 &&
                       $chat->users->contains($usuarioActual->id) &&
                       $chat->users->contains($destinatarioId);
            })->first();


        if ($chatExistente) {
            // Si el chat existe, añadir el mensaje y redirigir
            Message::create([
                'chat_id' => $chatExistente->id,
                'user_id' => $usuarioActual->id,
                'contenido' => $mensajeInicial,
            ]);
            return redirect()->route('chat.show', $chatExistente->id);
        }

        // Crear nuevo chat privado
        $chat = Chat::create([
            'nombre' => null, // Nombre nulo para chats privados
            'es_privado' => true,
        ]);

        // Asociar usuarios al nuevo chat
        $chat->users()->attach([$usuarioActual->id, $destinatarioId]);

        // Crear el mensaje inicial en el nuevo chat
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => $usuarioActual->id,
            'contenido' => $mensajeInicial,
        ]);

        return redirect()->route('chat.show', $chat->id);
    }
}
