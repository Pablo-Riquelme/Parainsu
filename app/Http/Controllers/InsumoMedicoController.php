<?php

namespace App\Http\Controllers;

use App\Models\InsumoMedico;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel; // <-- Añadido para Excel
use Barryvdh\DomPDF\Facade\Pdf;       // <-- Añadido para PDF

// Asegúrate de crear este archivo: app/Exports/InsumosMedicosExport.php
use App\Exports\InsumosMedicosExport; // <-- Añadido para la clase de exportación de Excel

class InsumoMedicoController extends Controller
{
    /**
     * Muestra una lista de los insumos médicos.
     */
    public function index(Request $request)
    {
        $query = InsumoMedico::query();

        // Filtro por nombre de insumo Y AHORA TAMBIÉN POR DESCRIPCIÓN
        if ($request->filled('nombre_filtro')) {
            $search = $request->input('nombre_filtro'); // Guardamos el valor para usarlo en la clausula
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('descripcion', 'like', '%' . $search . '%'); // Agregado: búsqueda por descripción
            });
        }

        // Puedes añadir más filtros aquí si los necesitas (ej. por proveedor, stock, etc.)

        $insumosMedicos = $query->paginate(10); // Paginar resultados

        return view('insumos_medicos.index', compact('insumosMedicos'));
    }

    /**
     * Muestra el formulario para crear un nuevo insumo médico.
     */
    public function create()
    {
        return view('insumos_medicos.create');
    }

    /**
     * Almacena un insumo médico recién creado en el almacenamiento.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:50',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'precio' => 'nullable|numeric|min:0',
            'proveedor' => 'nullable|string|max:255',
        ]);

        InsumoMedico::create($request->all());

        return redirect()->route('insumos-medicos.index')->with('success', 'Insumo médico creado exitosamente.');
    }

    /**
     * Muestra el insumo médico especificado.
     */
    public function show(InsumoMedico $insumoMedico)
    {
        return view('insumos_medicos.show', compact('insumoMedico'));
    }

    /**
     * Muestra el formulario para editar el insumo médico especificado.
     */
    public function edit(InsumoMedico $insumoMedico)
    {
        return view('insumos_medicos.edit', compact('insumoMedico'));
    }

    /**
     * Actualiza el insumo médico especificado en el almacenamiento.
     */
    public function update(Request $request, InsumoMedico $insumoMedico)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'unidad_medida' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'precio' => 'required|numeric|min:0',
            'proveedor' => 'nullable|string|max:255',
        ]);

        $oldStock = $insumoMedico->stock;

        $insumoMedico->update($validatedData);

        if ($insumoMedico->stock !== $oldStock) {
            $tipoMovimiento = ($insumoMedico->stock > $oldStock) ? 'entrada' : 'salida';
            $cantidadMovida = abs($insumoMedico->stock - $oldStock);

            $movimiento = new \App\Models\Movimiento();
            $movimiento->tipo = $tipoMovimiento;
            $movimiento->cantidad = $cantidadMovida;
            $movimiento->descripcion = "Ajuste de stock desde edición de insumo.";
            $movimiento->user_id = auth()->id();
            $movimiento->insumo_medico_id = $insumoMedico->id;
            $movimiento->equipo_ti_id = null;

            $movimiento->save();
        }

        return redirect()->route('insumos-medicos.index')->with('success', 'Insumo médico actualizado exitosamente.');
    }

    /**
     * Elimina el insumo médico especificado del almacenamiento.
     */
    public function destroy(InsumoMedico $insumoMedico)
    {
        $insumoMedico->delete();

        return redirect()->route('insumos-medicos.index')->with('success', 'Insumo médico eliminado exitosamente.');
    }

    /**
     * Exportar insumos médicos a Excel.
     * Añade este método.
     */
    public function exportExcel(Request $request)
    {
        // Aplicar los mismos filtros que en la vista index para asegurar que la exportación sea consistente
        $query = InsumoMedico::query();
        if ($request->filled('nombre_filtro')) {
            $search = $request->input('nombre_filtro');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('descripcion', 'like', '%' . $search . '%')
                  ->orWhere('unidad_medida', 'like', '%' . $search . '%')
                  ->orWhere('proveedor', 'like', '%' . $search . '%'); // Puedes añadir más campos aquí si los filtras en el index
            });
        }

        // Se pasa la colección de resultados a la clase de exportación
        return Excel::download(new InsumosMedicosExport($query->get()), 'insumos_medicos.xlsx');
    }

    /**
     * Exportar insumos médicos a PDF.
     * Añade este método.
     */
    public function exportPdf(Request $request)
    {
        // Aplicar los mismos filtros que en la vista index
        $query = InsumoMedico::query();
        if ($request->filled('nombre_filtro')) {
            $search = $request->input('nombre_filtro');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('descripcion', 'like', '%' . $search . '%')
                  ->orWhere('unidad_medida', 'like', '%' . $search . '%')
                  ->orWhere('proveedor', 'like', '%' . $search . '%'); // Añade los mismos campos que en exportExcel
            });
        }

        $insumos = $query->get();

        // Cargar la vista con los datos y generar el PDF
        $pdf = Pdf::loadView('insumos_medicos.pdf', compact('insumos'));
        return $pdf->download('insumos_medicos.pdf');
    }
}
