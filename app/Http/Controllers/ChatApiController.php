<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent; // Asegúrate de que este evento exista o créalo

class ChatApiController extends Controller
{
    /**
     * Constructor para aplicar middleware de autenticación a la API.
     */
    public function __construct()
    {
        // Protegemos estas rutas de la API con Laravel Sanctum.
        // Asegúrate de que el trait 'HasApiTokens' esté en tu modelo App\Models\User.
        $this->middleware('auth:sanctum');
    }

    /**
     * Encuentra o crea un chat privado entre dos usuarios.
     * POST /api/chats/private
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPrivateChat(Request $request)
    {
        // Validar que se ha proporcionado un ID de usuario receptor válido
        $request->validate([
            'recipient_user_id' => 'required|exists:users,id|not_in:' . Auth::id(),
        ]);

        $senderId = Auth::id();
        $recipientId = $request->input('recipient_user_id');

        // Buscar si ya existe un chat privado entre estos dos usuarios
        $chat = Chat::where('es_privado', true)
                    ->whereHas('users', function ($query) use ($senderId) {
                        $query->where('user_id', $senderId);
                    })
                    ->whereHas('users', function ($query) use ($recipientId) {
                        $query->where('user_id', $recipientId);
                    })
                    ->first();

        // Si no existe, crearlo
        if (!$chat) {
            $chat = Chat::create(['es_privado' => true, 'nombre' => null]);
            $chat->users()->attach([$senderId, $recipientId]); // Adjuntar a ambos usuarios al chat
            return response()->json(['message' => 'Chat privado creado exitosamente.', 'chat_id' => $chat->id], 201);
        }

        // Si ya existe, simplemente retornar el ID del chat existente
        return response()->json(['message' => 'Chat privado ya existe.', 'chat_id' => $chat->id], 200);
    }

    /**
     * Recupera los mensajes de un chat específico.
     * GET /api/chats/{chat}/messages
     * @param Chat $chat
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages(Chat $chat)
    {
        // Verificar que el usuario autenticado es participante de este chat
        if (!$chat->users->contains(Auth::id())) {
            return response()->json(['message' => 'No autorizado para ver este chat.'], 403);
        }

        // Obtener los mensajes, ordenarlos por fecha de creación (ascendente) y paginarlos.
        // Se carga la relación 'user' para obtener el nombre del remitente de cada mensaje.
        $messages = $chat->messages()->with('user')->orderBy('created_at', 'asc')->paginate(20);

        return response()->json($messages);
    }

    /**
     * Almacena un nuevo mensaje en un chat.
     * POST /api/chats/{chat}/messages
     * @param Request $request
     * @param Chat $chat
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request, Chat $chat)
    {
        // Validar el contenido del mensaje
        $request->validate([
            'contenido' => 'required|string|max:1000',
        ]);

        // Verificar que el usuario autenticado es participante de este chat antes de permitirle enviar un mensaje
        if (!$chat->users->contains(Auth::id())) {
            return response()->json(['message' => 'No autorizado para enviar mensajes a este chat.'], 403);
        }

        // Crear y guardar el nuevo mensaje en el chat
        $message = $chat->messages()->create([
            'user_id' => Auth::id(),
            'contenido' => $request->input('contenido'),
        ]);

        // Para obtener el objeto User, lo cargamos explícitamente desde el usuario autenticado.
        $user = Auth::user();

        // Emitir el evento 'MessageSent' para la funcionalidad en tiempo real
        event(new MessageSent($message, $user));

        // Actualizar el timestamp 'updated_at' del chat para reflejar actividad reciente
        $chat->touch();

        return response()->json(['message' => 'Mensaje enviado exitosamente.', 'data' => $message], 201);
    }
}