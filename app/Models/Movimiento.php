<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'tipo',
        'cantidad',
        'descripcion',
        'user_id',
        'insumo_medico_id',
        'equipo_ti_id',
        'tabla_afectada',   // <-- ¡Añadido!
        'id_afectada',      // <-- ¡Añadido!
        'datos_antes',      // <-- ¡Añadido!
        'datos_despues',    // <-- ¡Añadido!
        'ip_address',       // <-- ¡Añadido!
    ];

    // Castear atributos a tipos de PHP (muy importante para JSON)
    protected $casts = [
        'datos_antes' => 'array',   // Castear a array para que Laravel decodifique JSON automáticamente
        'datos_despues' => 'array', // Castear a array para que Laravel decodifique JSON automáticamente
        'created_at' => 'datetime', // Opcional, pero útil para trabajar con fechas
        'updated_at' => 'datetime', // Opcional, pero útil para trabajar con fechas
    ];

    /**
     * Relación: Un movimiento pertenece a un Usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Un movimiento puede pertenecer a un InsumoMedico.
     */
    public function insumoMedico()
    {
        return $this->belongsTo(InsumoMedico::class, 'insumo_medico_id'); // Específicamos la FK para claridad
    }

    /**
     * Relación: Un movimiento puede pertenecer a un EquipoTi.
     */
    public function equipoTi()
    {
        return $this->belongsTo(EquipoTI::class, 'equipo_ti_id'); // Específicamos la FK para claridad
    }
}