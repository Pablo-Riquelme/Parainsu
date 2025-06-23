<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection; // Usar Collection directamente si pasas una colección

class InsumosMedicosExport implements FromCollection, WithHeadings, WithMapping
{
    protected $insumos;

    public function __construct(Collection $insumos) // Aceptar una colección
    {
        $this->insumos = $insumos;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->insumos;
    }

    /**
     * Define los encabezados de la tabla Excel.
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Descripción',
            'Unidad de Medida',
            'Stock',
            'Stock Mínimo',
            'Precio',
            'Proveedor',
            'Fecha Creación',
            'Última Actualización',
        ];
    }

    /**
     * Mapea cada fila de datos a un formato específico para Excel.
     * @param mixed $insumo
     * @return array
     */
    public function map($insumo): array
    {
        return [
            $insumo->id,
            $insumo->nombre,
            $insumo->descripcion,
            $insumo->unidad_medida,
            $insumo->stock,
            $insumo->stock_minimo,
            $insumo->precio,
            $insumo->proveedor,
            $insumo->created_at,
            $insumo->updated_at,
        ];
    }
}
