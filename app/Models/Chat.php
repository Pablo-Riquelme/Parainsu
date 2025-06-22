<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',     // Nombre del chat (útil para chats grupales)
        'es_privado', // Booleano para indicar si es un chat 1-a-1
    ];

    /**
     * Get the messages for the chat.
     * Un chat puede tener muchos mensajes.
     */
    public function users()
{
    return $this->belongsToMany(User::class);
}

public function messages()
{
    return $this->hasMany(Message::class);
}

}
