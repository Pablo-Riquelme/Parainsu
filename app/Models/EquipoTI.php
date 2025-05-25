<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipoTI extends Model
{
    use HasFactory;

    // Nombre de la tabla asociada al modelo
    protected $table = 'equipos_ti';

    // Campos que pueden ser asignados masivamente (fillable)
    // Esto es importante por seguridad (evita la asignación masiva de campos no deseados)
    protected $fillable = [
        'nombre_equipo',
        'ubicacion',
        'estado',
        'descripcion',
        'numero_serie',
        'modelo',
        'marca',
        'fecha_adquisicion',
        'usuario_asignado_id',
    ];

   
    protected $casts = [
        'fecha_adquisicion' => 'date', 
        'estado' => \App\Enums\EstadoEquipo::class, 
    ];


    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_asignado_id');
    }
}