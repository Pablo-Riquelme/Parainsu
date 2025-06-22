<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // Ya tienes HasApiTokens aquí, ahora está importado.

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role that belongs to the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role->name === 'admin_ti';
    }

    /**
     * Check if the user is a bodega user.
     *
     * @return bool
     */
    public function isBodega()
    {
        return $this->role->name === 'bodega';
    }

    /**
     * Check if the user is a normal user.
     *
     * @return bool
     */
    public function isUser()
    {
        return $this->role->name === 'usuario_normal';
    }

    /**
     * Get the EquiposTI associated with the user.
     */
    public function equiposTI()
    {
        return $this->hasMany(EquipoTI::class, 'usuario_asignado_id');
    }

    /**
     * Get the chats the user belongs to.
     * Un usuario puede estar en muchos chats (relación muchos a muchos).
     */
    public function chats()
    {
        return $this->belongsToMany(Chat::class);
    }

    /**
     * Get the messages sent by the user.
     * Un usuario ha enviado muchos mensajes.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
