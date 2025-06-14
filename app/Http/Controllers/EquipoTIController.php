<?php

namespace App\Http\Controllers;

use App\Models\EquipoTI; // Make sure this matches your model name precisely
use App\Models\User;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use App\Enums\EstadoEquipo; // Make sure this matches your Enum's namespace
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon; // Import Carbon for date handling

class EquipoTIController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra una lista de todos los equipos de TI con filtros y paginación.
     */
    public function index(Request $request)
    {
        // Obtener la lista de usuarios para el filtro
        $usuarios = User::orderBy('name')->get();
        // Obtener los valores posibles del Enum EstadoEquipo para el filtro
        $estados = EstadoEquipo::cases();

        // Iniciar la consulta de equipos con la relación 'usuario' cargada
        $query = EquipoTI::with('usuario');

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
            if ($usuarioFiltro === 'null') { // Manejar equipos sin usuario asignado (null)
                $query->whereNull('usuario_asignado_id');
            } elseif (is_numeric($usuarioFiltro)) {
                $query->where('usuario_asignado_id', $usuarioFiltro);
            }
        }

        // Obtener los equipos paginados con los filtros aplicados
        // withQueryString() mantiene los filtros en la URL al cambiar de página
        $equipos = $query->paginate(10)->withQueryString();

        // Pasar los equipos, usuarios y estados a la vista
        return view('equipos_ti.index', compact('equipos', 'usuarios', 'estados'));
    }

    /**
     * Show the form for creating a new resource.
     * Muestra el formulario para crear un nuevo equipo de TI.
     */
    public function create()
    {
        $usuarios = User::orderBy('name')->get();
        $estados = EstadoEquipo::cases(); // Obtiene todos los casos del Enum
        return view('equipos_ti.create', compact('usuarios', 'estados'));
    }

    /**
     * Store a newly created resource in storage.
     * Guarda un nuevo equipo de TI en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_equipo' => 'required|string|max:255|unique:equipos_ti,nombre_equipo',
            'ubicacion' => 'required|string|max:255',
            // Valida que el estado esté entre los valores posibles del Enum
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

    /**
     * Display the specified resource.
     * Muestra los detalles de un equipo de TI específico.
     */
    public function show(EquipoTI $equipoTI)
    {
        // Eager load the 'usuario' relationship if you display user details in the show view
        $equipoTI->load('usuario');
        return view('equipos_ti.show', compact('equipoTI'));
    }

    /**
     * Show the form for editing the specified resource.
     * Muestra el formulario para editar un equipo de TI existente.
     */
    public function edit(EquipoTI $equipoTI)
    {
        // Eager load the 'usuario' relationship if you display user details in the edit form
        $equipoTI->load('usuario');
        $usuarios = User::orderBy('name')->get();
        $estados = EstadoEquipo::cases();
        return view('equipos_ti.edit', compact('equipoTI', 'usuarios', 'estados'));
    }

    /**
     * Update the specified resource in storage.
     * Actualiza un equipo de TI existente y registra los cambios como un movimiento.
     */
    public function update(Request $request, EquipoTI $equipoTI)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
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

        // Atributos que se auditarán y sus nombres para visualización
        $auditedAttributes = [
            'nombre_equipo', 'ubicacion', 'estado', 'usuario_asignado_id',
            'numero_serie', 'modelo', 'marca', 'fecha_adquisicion', 'descripcion'
        ];

        // 1. Capturar el estado ORIGINAL del equipo antes de la actualización
        //    Accedemos a los atributos directamente para obtener los valores ya casteados por el modelo (Enums, Carbon)
        $oldAttributes = [];
        foreach ($auditedAttributes as $key) {
            $value = $equipoTI->{$key}; // Obtiene el valor casteado (ej. Enum object, Carbon object)

            if ($key === 'estado' && $value instanceof \BackedEnum) {
                // Si es un Enum respaldado, usa su valor string
                $oldAttributes[$key] = $value->value;
            } elseif ($key === 'fecha_adquisicion' && $value instanceof Carbon) {
                // Si es un objeto Carbon, formatea la fecha a string
                $oldAttributes[$key] = $value->format('Y-m-d');
            } elseif ($key === 'usuario_asignado_id') {
                // Para usuario_asignado_id, obtenemos el nombre del usuario si existe
                $oldAttributes[$key] = User::find($value)->name ?? null; // Si no hay usuario, será null
            } else {
                // Para otros tipos, o si el valor es null, usar el valor tal cual
                $oldAttributes[$key] = $value;
            }
        }


        // 2. Realizar la actualización del equipo
        $equipoTI->update($validatedData);


        // 3. Obtener los atributos ACTUALIZADOS del equipo
        //    Accedemos a los atributos directamente para obtener los valores ya casteados por el modelo (Enums, Carbon)
        $newAttributes = [];
        foreach ($auditedAttributes as $key) {
            $value = $equipoTI->{$key}; // Obtiene el valor casteado

            if ($key === 'estado' && $value instanceof \BackedEnum) {
                $newAttributes[$key] = $value->value;
            } elseif ($key === 'fecha_adquisicion' && $value instanceof Carbon) {
                $newAttributes[$key] = $value->format('Y-m-d');
            } elseif ($key === 'usuario_asignado_id') {
                // Para usuario_asignado_id, obtenemos el nombre del usuario si existe
                $newAttributes[$key] = User::find($value)->name ?? null;
            } else {
                $newAttributes[$key] = $value;
            }
        }

        // 4. Identificar los cambios comparando arrays (solo valores diferentes para las mismas claves)
        $changes = array_diff_assoc($newAttributes, $oldAttributes);

        // Si hay cambios detectados, crear un registro de movimiento
        if (!empty($changes)) {
            $movimiento = new Movimiento();
            $movimiento->tipo = 'edicion_equipo'; // Tipo de movimiento para edición de equipo
            $movimiento->cantidad = 0; // Para ediciones de atributos, la cantidad es 0 o 1
            // La columna 'tabla_afectada' de tu migración:
            $movimiento->tabla_afectada = 'equipos_ti';
            // La columna 'id_afectada' de tu migración:
            $movimiento->id_afectada = $equipoTI->id;

            // Generar descripción detallada de los cambios
            $changedFieldsDescription = [];
            foreach ($changes as $key => $newValue) {
                // Asegurar que el valor original exista o sea 'N/A'
                $oldValue = $oldAttributes[$key] ?? 'N/A';
                // Formatear el nombre del campo para que sea más legible (ej. "nombre_equipo" -> "Nombre Equipo")
                $cleanKey = ucfirst(str_replace('_', ' ', $key));

                // Para usuario_asignado_id, los valores ya son nombres o null para la descripción
                $oldDisplayValue = $oldValue ?? 'Sin asignar';
                $newDisplayValue = $newValue ?? 'Sin asignar';

                $changedFieldsDescription[] = "{$cleanKey}: '{$oldDisplayValue}' a '{$newDisplayValue}'";
            }
            $movimiento->descripcion = "Equipo TI editado. Cambios: " . implode('; ', $changedFieldsDescription);

            $movimiento->user_id = Auth::id(); // El ID del usuario autenticado
            $movimiento->equipo_ti_id = $equipoTI->id; // El ID del equipo TI afectado
            $movimiento->insumo_medico_id = null; // Asegurar que sea null para movimientos de equipos

            // Guardar los datos antes y después de los atributos que cambiaron, ya transformados a string/escalar
            $movimiento->datos_antes = json_encode(array_intersect_key($oldAttributes, $changes));
            $movimiento->datos_despues = json_encode($changes);
            $movimiento->ip_address = $request->ip(); // Registrar la IP del usuario

            $movimiento->save();
        }

        return redirect()->route('equipos-ti.index')->with('success', 'Equipo de TI actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     * Elimina un equipo de TI de la base de datos y registra un movimiento de baja.
     */
    public function destroy(EquipoTI $equipoTI)
    {
        // Registrar un movimiento de "baja" o "eliminación" antes de borrar el equipo
        $movimiento = new Movimiento();
        $movimiento->tipo = 'baja_equipo'; // Tipo de movimiento para baja de equipo
        $movimiento->cantidad = 1; // Un equipo dado de baja
        $movimiento->descripcion = "Equipo TI eliminado del sistema.";
        $movimiento->user_id = Auth::id(); // El usuario autenticado que realiza la acción
        $movimiento->equipo_ti_id = $equipoTI->id; // El equipo dado de baja
        $movimiento->insumo_medico_id = null; // Asegurar que sea null

        $movimiento->tabla_afectada = 'equipos_ti'; // Columna de auditoría
        $movimiento->id_afectada = $equipoTI->id; // Columna de auditoría

        // Guardar todos los datos del equipo antes de borrarlo como 'datos_antes'
        // Convertir atributos sensibles (Enums, Carbon) a string/valor antes de JSON
        $equipoAttributesForLog = [];
        foreach ($equipoTI->getAttributes() as $key => $value) {
            $attributeValue = $equipoTI->{$key}; // Accede al valor casteado

            if ($attributeValue instanceof \BackedEnum) {
                $equipoAttributesForLog[$key] = $attributeValue->value;
            } elseif ($attributeValue instanceof Carbon) {
                $equipoAttributesForLog[$key] = $attributeValue->format('Y-m-d H:i:s'); // Formato completo para el log
            } elseif ($key === 'usuario_asignado_id') {
                // Para usuario_asignado_id, obtenemos el nombre del usuario si existe
                $equipoAttributesForLog[$key] = User::find($attributeValue)->name ?? null;
            }
            else {
                $equipoAttributesForLog[$key] = $attributeValue;
            }
        }
        $movimiento->datos_antes = json_encode($equipoAttributesForLog);
        $movimiento->datos_despues = json_encode(null); // No hay estado "después" porque el equipo se elimina
        $movimiento->ip_address = request()->ip(); // Capturar la IP del usuario

        $movimiento->save(); // Guardar el movimiento de baja

        // Proceder con la eliminación del equipo
        $equipoTI->delete();

        return redirect()->route('equipos-ti.index')->with('success', 'Equipo de TI eliminado exitosamente y movimiento de baja registrado.');
    }
}