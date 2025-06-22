<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\User;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user; // El usuario que envió el mensaje

    /**
     * Crea una nueva instancia de evento.
     *
     * @param Message $message
     * @param User $user
     * @return void
     */
    public function __construct(Message $message, User $user)
    {
        $this->message = $message;
        $this->user = $user;
    }

    /**
     * Obtiene los canales en los que el evento debe transmitirse.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Transmite en un canal privado para el chat específico.
        // Solo los usuarios autorizados (participantes del chat) podrán escuchar este canal.
        return [
            new PrivateChannel('chat.' . $this->message->chat_id),
        ];
    }

    /**
     * El nombre de difusión del evento.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'MessageSent'; // El nombre del evento que el frontend escuchará
    }

    /**
     * Obtiene los datos a transmitir.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        // Asegura que los datos del mensaje y del usuario (remitente) se incluyan en la transmisión.
        return [
            'message' => $this->message->toArray(),
            'user' => $this->user->toArray(),
        ];
    }
}
