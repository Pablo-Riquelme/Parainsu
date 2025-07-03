<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mantenimientos'; // Asegúrate de que el nombre de la tabla sea correcto

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'equipo_ti_id',
        'fecha_inicio',
        'fecha_fin',
        'tipo',
        'descripcion',
        'observaciones',
        'estado', // 'pendiente', 'en_progreso', 'completado', 'cancelado'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    /**
     * Get the EquipoTI that owns the Mantenimiento.
     */
    public function equipoTi()
    {
        return $this->belongsTo(EquipoTI::class, 'equipo_ti_id');
    }
}
