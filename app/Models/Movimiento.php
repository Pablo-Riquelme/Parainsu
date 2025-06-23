<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;

    protected $table = 'movimientos'; // Asegúrate de que el nombre de la tabla sea correcto si no es 'movimientos'

    protected $fillable = [
        'user_id',
        'tipo',
        'cantidad',
        'descripcion',
        'insumo_medico_id',
        'equipo_ti_id',
        'tabla_afectada',
        'id_afectada',
        'ip_address',
        'datos_antes',
        'datos_despues',
    ];

    protected $casts = [
        'datos_antes' => 'array',
        'datos_despues' => 'array',
    ];


    // Relación con el usuario que realizó el movimiento
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con InsumoMedico (puede ser null)
    public function insumoMedico()
    {
        return $this->belongsTo(InsumoMedico::class);
    }

    // Relación con EquipoTi (puede ser null)
    public function equipoTi()
    {
        return $this->belongsTo(EquipoTi::class);
    }

    /**
     * Método para obtener una descripción legible del movimiento.
     * Útil para mostrar en el log de actividades.
     * @return string
     */
    public function getSummaryAttribute()
    {
        $userName = $this->user ? $this->user->name : 'Usuario Desconocido';
        $itemType = $this->tabla_afectada === 'insumos_medicos' ? 'insumo médico' : ($this->tabla_afectada === 'equipos_ti' ? 'equipo TI' : 'item');
        $itemName = '';

        if ($this->insumoMedico) {
            $itemName = $this->insumoMedico->nombre; // Asume que InsumoMedico tiene un campo 'nombre'
        } elseif ($this->equipoTi) {
            $itemName = $this->equipoTi->nombre_equipo; // Asume que EquipoTi tiene un campo 'nombre_equipo'
        }

        switch ($this->tipo) {
            case 'entrada':
                return "{$userName} registró una entrada de {$this->cantidad} unidad(es) de {$itemType}: {$itemName}.";
            case 'salida':
                return "{$userName} registró una salida de {$this->cantidad} unidad(es) de {$itemType}: {$itemName}.";
            // Puedes añadir más tipos de movimientos aquí (ej. 'edicion', 'baja', 'creacion')
            default:
                // Si el tipo no es 'entrada' o 'salida', la descripción del movimiento puede ser más relevante
                if ($this->descripcion) {
                     return "{$userName} realizó un movimiento de tipo '{$this->tipo}' en {$itemType} '{$itemName}': {$this->descripcion}.";
                }
                return "{$userName} realizó un movimiento de tipo '{$this->tipo}' en {$itemType} '{$itemName}'.";
        }
    }
}