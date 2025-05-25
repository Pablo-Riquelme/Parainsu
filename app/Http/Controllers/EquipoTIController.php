<?php

namespace App\Http\Controllers;

use App\Models\EquipoTI;
use App\Models\User;
use Illuminate\Http\Request;
use App\Enums\EstadoEquipo;

class EquipoTIController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra una lista de todos los equipos de TI.
     */
    public function index(Request $request) // Inyectamos Request para obtener los parámetros de búsqueda
    {
        // Obtener la lista de usuarios para el filtro
        $usuarios = User::orderBy('name')->get();
        // Obtener los valores posibles del Enum EstadoEquipo para el filtro
        $estados = EstadoEquipo::cases();

        // Iniciar la consulta de equipos con la relación 'usuario' cargada
        $query = EquipoTI::with('usuario');

        // Aplicar filtros basados en los parámetros de la solicitud

        // 1. Búsqueda general por texto (nombre, ubicacion, serie, modelo, marca)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nombre_equipo', 'like', '%' . $searchTerm . '%')
                  ->orWhere('ubicacion', 'like', '%' . $searchTerm . '%')
                  ->orWhere('numero_serie', 'like', '%' . $searchTerm . '%')
                  ->orWhere('modelo', 'like', '%' . $searchTerm . '%')
                  ->orWhere('marca', 'like', '%' . $searchTerm . '%');
            });
        }

        // 2. Filtrar por estado
        if ($request->filled('estado_filtro')) {
            $estadoFiltro = $request->input('estado_filtro');
            $query->where('estado', $estadoFiltro);
        }

        // 3. Filtrar por usuario asignado
        if ($request->filled('usuario_filtro')) {
            $usuarioFiltro = $request->input('usuario_filtro');
            if ($usuarioFiltro === 'null') { // Manejar equipos sin usuario asignado
                $query->whereNull('usuario_asignado_id');
            } elseif (is_numeric($usuarioFiltro)) {
                $query->where('usuario_asignado_id', $usuarioFiltro);
            }
        }

        // Obtener los equipos paginados con los filtros aplicados
        $equipos = $query->paginate(10)->withQueryString(); // withQueryString() para mantener los filtros en la paginación

        // Pasar los equipos, usuarios y estados a la vista
        return view('equipos_ti.index', compact('equipos', 'usuarios', 'estados'));
    }

    // ... (Métodos create, store, show, edit, update, destroy permanecen igual) ...

    public function create()
    {
        $usuarios = User::orderBy('name')->get();
        $estados = EstadoEquipo::cases();
        return view('equipos_ti.create', compact('usuarios', 'estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_equipo' => 'required|string|max:255|unique:equipos_ti,nombre_equipo',
            'ubicacion' => 'required|string|max:255',
            'estado' => 'required|in:' . implode(',', array_column(EstadoEquipo::cases(), 'value')),
            'descripcion' => 'nullable|string',
            'numero_serie' => 'nullable|string|max:255|unique:equipos_ti,numero_serie',
            'modelo' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:255',
            'fecha_adquisicion' => 'nullable|date',
            'usuario_asignado_id' => 'nullable|exists:users,id',
        ]);

        EquipoTI::create($request->all());

        return redirect()->route('equipos-ti.index')->with('success', 'Equipo de TI creado exitosamente.');
    }

    public function show(EquipoTI $equipoTI)
    {
        return view('equipos_ti.show', compact('equipoTI'));
    }

    public function edit(EquipoTI $equipoTI)
    {
        $usuarios = User::orderBy('name')->get();
        $estados = EstadoEquipo::cases();
        return view('equipos_ti.edit', compact('equipoTI', 'usuarios', 'estados'));
    }

    public function update(Request $request, EquipoTI $equipoTI)
    {
        $request->validate([
            'nombre_equipo' => 'required|string|max:255|unique:equipos_ti,nombre_equipo,' . $equipoTI->id,
            'ubicacion' => 'required|string|max:255',
            'estado' => 'required|in:' . implode(',', array_column(EstadoEquipo::cases(), 'value')),
            'descripcion' => 'nullable|string',
            'numero_serie' => 'nullable|string|max:255|unique:equipos_ti,numero_serie,' . $equipoTI->id,
            'modelo' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:255',
            'fecha_adquisicion' => 'nullable|date',
            'usuario_asignado_id' => 'nullable|exists:users,id',
        ]);

        $equipoTI->update($request->all());

        return redirect()->route('equipos-ti.index')->with('success', 'Equipo de TI actualizado exitosamente.');
    }

    public function destroy(EquipoTI $equipoTI)
    {
        $equipoTI->delete();

        return redirect()->route('equipos-ti.index')->with('success', 'Equipo de TI eliminado exitosamente.');
    }
}