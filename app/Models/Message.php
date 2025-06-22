<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chat_id',  // ID del chat al que pertenece el mensaje
        'user_id',  // ID del usuario que envió el mensaje
        'contenido', // Contenido del mensaje
        'leido_en', // Marca de tiempo cuando el mensaje fue leído (puede ser nulo)
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'leido_en' => 'datetime', // Asegura que 'leido_en' se trate como un objeto DateTime
    ];

    /**
     * Get the chat that owns the message.
     * Un mensaje pertenece a un chat.
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Get the user that sent the message.
     * Un mensaje fue enviado por un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
