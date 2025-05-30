<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsumoMedico extends Model
{
    use HasFactory;
    protected $table = 'insumos_medicos';
    protected $fillable = [
        'nombre',
        'descripcion',
        'unidad_medida',
        'stock',
        'stock_minimo',
        'precio',
        'proveedor',
    ];
    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'insumo_medico_id');
    }
}
