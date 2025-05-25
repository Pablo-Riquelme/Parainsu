<?php

namespace App\Enums;

enum EstadoEquipo: string
{
    case EnUso = 'en_uso';
    case EnDesuso = 'en_desuso';
    case EnReparacion = 'en_reparacion';
    case Disponible = 'disponible';

    // Puedes añadir métodos si necesitas, por ejemplo, para mostrar un texto más amigable
    public function label(): string
    {
        return match ($this) {
            self::EnUso => 'En Uso',
            self::EnDesuso => 'En Desuso',
            self::EnReparacion => 'En Reparación',
            self::Disponible => 'Disponible',
        };
    }
}