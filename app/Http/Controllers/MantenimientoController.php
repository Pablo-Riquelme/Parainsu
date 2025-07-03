<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use App\Models\EquipoTI; // Necesitamos el modelo EquipoTI para el select
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Para depuración

class MantenimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mantenimientos = Mantenimiento::with('equipoTi')->orderBy('fecha_inicio', 'desc')->get();
        return view('mantenimientos.index', compact('mantenimientos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $equiposTi = EquipoTI::all(); // Obtener todos los equipos para el select
        return view('mantenimientos.create', compact('equiposTi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'equipo_ti_id' => 'required|exists:equipos_ti,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'tipo' => 'required|string|max:255', // Ajusta si tienes un ENUM para tipo
            'descripcion' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'estado' => 'required|string|max:255', // Ajusta si tienes un ENUM para estado
        ]);

        try {
            Mantenimiento::create($request->all());
            return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento programado exitosamente.');
        } catch (\Exception $e) {
            Log::error("Error al guardar mantenimiento: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al programar el mantenimiento: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Mantenimiento $mantenimiento)
    {
        return view('mantenimientos.show', compact('mantenimiento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mantenimiento $mantenimiento)
    {
        $equiposTi = EquipoTI::all();
        return view('mantenimientos.edit', compact('mantenimiento', 'equiposTi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mantenimiento $mantenimiento)
    {
        $request->validate([
            'equipo_ti_id' => 'required|exists:equipos_ti,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'tipo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'estado' => 'required|string|max:255',
        ]);

        try {
            $mantenimiento->update($request->all());
            return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento actualizado exitosamente.');
        } catch (\Exception $e) {
            Log::error("Error al actualizar mantenimiento: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el mantenimiento: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mantenimiento $mantenimiento)
    {
        try {
            $mantenimiento->delete();
            return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error("Error al eliminar mantenimiento: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar el mantenimiento: ' . $e->getMessage());
        }
    }
}
