<?php

namespace App\Events;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // <-- ¡IMPORTANTE! Añade este import

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Message  $message
     * @param  \App\Models\User  $user
     * @return void
     */
    public function __construct(Message $message, User $user)
    {
        $this->message = $message;
        $this->user = $user;
        Log::info('[MessageSent Event] Constructor called for message ID: ' . $message->id . ' by user: ' . $user->name); // <-- Depuración aquí
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channelName = 'chat.' . $this->message->chat_id;
        Log::info('[MessageSent Event] Broadcasting on channel: ' . $channelName); // <-- Depuración aquí
        return [
            new PrivateChannel($channelName),
        ];
    }

    /**
     * The data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'chat_id' => $this->message->chat_id,
                'user_id' => $this->message->user_id,
                'contenido' => $this->message->contenido,
                'created_at' => $this->message->created_at->toDateTimeString(), // Formato de fecha consistente
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
        ];
    }

    /**
     * The name of the event to broadcast.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}
