<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'tipo_movimiento',
        'cantidad',
        'descripcion',
        'user_id',
        'insumo_medico_id',
        'equipo_ti_id',
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
        return $this->belongsTo(InsumoMedico::class);
    }

    /**
     * Relación: Un movimiento puede pertenecer a un EquipoTi.
     */
    public function equipoTi()
    {
        return $this->belongsTo(EquipoTi::class);
    }
}