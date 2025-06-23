{{-- resources/views/chat/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Chat con ' . ($chat->es_privado && $chat->users->firstWhere('id', '!=', Auth::id()) ? $chat->users->firstWhere('id', '!=', Auth::id())->name : $chat->nombre))

{{-- Añade los data-attributes al body para que el JS global los use --}}
@section('body_attributes')
{{-- El data-api-token es útil si haces llamadas a APIs con Sanctum, --}}
{{-- pero no es estrictamente necesario para la autenticación del canal privado de Reverb/Echo --}}
{{-- ni para el envío de mensajes a rutas web con CSRF. Lo mantengo por si lo necesitas para otras cosas. --}}
data-current-user-id="{{ Auth::id() }}"
data-api-token="{{ Auth::user() ? Auth::user()->createToken('global-chat-token')->plainTextToken : '' }}"
@endsection

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
            {{-- Input oculto para pasar el chat ID al JavaScript --}}
            <input type="hidden" id="chat-id" value="{{ $chat->id }}">

            {{-- Contenedor donde los mensajes del chat se añadirán y se mostrarán --}}
            <div class="chat-box overflow-auto p-3 mb-3 bg-light rounded" style="max-height: 400px; min-height: 200px;">
                {{-- Aquí se cargarán los mensajes --}}
                @if($chat->messages->isEmpty())
                    <p class="text-center text-muted">No hay mensajes aún en este chat. ¡Envía el primero!</p>
                @else
                    @foreach ($chat->messages as $message)
                        <div class="d-flex {{ $message->user_id == Auth::id() ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                            <div class="message-bubble {{ $message->user_id == Auth::id() ? 'bg-primary text-white' : 'bg-secondary text-white' }} rounded py-2 px-3 shadow-sm">
                                <div class="small text-opacity-75 mb-1">
                                    {{ $message->user->name }} - {{ \Carbon\Carbon::parse($message->created_at)->setTimezone('America/Santiago')->format('H:i') }} {{-- Hora de Chile --}}
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
{{-- El CSS se puede mantener aquí o mover a un archivo CSS externo si prefieres. --}}
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
