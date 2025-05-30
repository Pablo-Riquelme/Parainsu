<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\InsumoMedico; // Asegúrate de que el modelo exista y su namespace sea correcto
use App\Models\EquipoTi;     // Asegúrate de que el modelo exista y su namespace sea correcto
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra una lista de todos los movimientos.
     */
    public function index()
    {
        // Carga las relaciones 'user', 'insumoMedico' y 'equipoTi' para evitar N+1 query problem
        $movimientos = Movimiento::with(['user', 'insumoMedico', 'equipoTi'])->latest()->paginate(10);
        return view('movimientos.index', compact('movimientos'));
    }

    /**
     * Show the form for creating a new resource.
     * Muestra el formulario para crear un nuevo movimiento.
     */
    public function create()
    {
        // Pasa todos los insumos y equipos para que puedan ser seleccionados en un dropdown
        $insumos = InsumoMedico::orderBy('nombre')->get();
        $equipos = EquipoTi::orderBy('nombre_equipo')->get(); // Asegúrate que 'nombre_equipo' es el campo correcto
        return view('movimientos.create', compact('insumos', 'equipos'));
    }

    /**
     * Store a newly created resource in storage.
     * Guarda un nuevo movimiento en la base de datos y actualiza el stock/estado.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tipo_movimiento' => 'required|string|in:entrada,salida',
            'cantidad' => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:500',
            'item_type' => 'required|string|in:insumo_medico,equipo_ti', // Indica si es un insumo o un equipo
            'item_id' => [
                'required',
                'integer',
                // Validación personalizada para asegurar que el ID del ítem existe
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('item_type') == 'insumo_medico') {
                        if (!InsumoMedico::find($value)) {
                            $fail('El insumo médico seleccionado no es válido.');
                        }
                    } elseif ($request->input('item_type') == 'equipo_ti') {
                        if (!EquipoTi::find($value)) {
                            $fail('El equipo TI seleccionado no es válido.');
                        }
                    }
                },
            ],
        ]);

        $movimiento = new Movimiento();
        $movimiento->tipo_movimiento = $validatedData['tipo_movimiento'];
        $movimiento->cantidad = $validatedData['cantidad'];
        $movimiento->descripcion = $validatedData['descripcion'];
        $movimiento->user_id = auth()->id(); // Asigna el usuario autenticado que realiza el movimiento

        // Asigna la clave foránea correcta y asegura que la otra sea null
        if ($validatedData['item_type'] == 'insumo_medico') {
            $movimiento->insumo_medico_id = $validatedData['item_id'];
            $movimiento->equipo_ti_id = null;
        } elseif ($validatedData['item_type'] == 'equipo_ti') {
            $movimiento->equipo_ti_id = $validatedData['item_id'];
            $movimiento->insumo_medico_id = null;
        }

        $movimiento->save();

        // Lógica para actualizar el stock real del insumo o el estado del equipo
        if ($validatedData['item_type'] == 'insumo_medico') {
            $insumo = InsumoMedico::find($validatedData['item_id']);
            if ($insumo) {
                if ($validatedData['tipo_movimiento'] == 'entrada') {
                    $insumo->stock += $validatedData['cantidad'];
                } else { // salida
                    $insumo->stock -= $validatedData['cantidad'];
                }
                $insumo->save();
            }
        } elseif ($validatedData['item_type'] == 'equipo_ti') {
            // Para equipos TI, la "cantidad" en el movimiento puede representar una unidad,
            // y el movimiento puede cambiar su estado o ubicación.
            // Aquí puedes ajustar la lógica según cómo gestionas el stock/estado de equipos.
            // Ejemplo: si es una "salida" y asignas el equipo.
            // $equipo = EquipoTi::find($validatedData['item_id']);
            // if ($validatedData['tipo_movimiento'] == 'salida' && $equipo) {
            //     $equipo->estado = 'en_uso'; // O el estado apropiado
            //     $equipo->save();
            // }
        }

        return redirect()->route('movimientos.index')->with('success', 'Movimiento registrado exitosamente.');
    }

    /**
     * Display the specified resource.
     * Muestra los detalles de un movimiento específico.
     */
    public function show(Movimiento $movimiento)
    {
        // Carga las relaciones para mostrar los detalles completos
        $movimiento->load(['user', 'insumoMedico', 'equipoTi']);
        return view('movimientos.show', compact('movimiento'));
    }

    /**
     * Show the form for editing the specified resource.
     * Muestra el formulario para editar un movimiento existente.
     */
    public function edit(Movimiento $movimiento)
    {
        $insumos = InsumoMedico::orderBy('nombre')->get();
        $equipos = EquipoTi::orderBy('nombre_equipo')->get();
        return view('movimientos.edit', compact('movimiento', 'insumos', 'equipos'));
    }

    /**
     * Update the specified resource in storage.
     * Actualiza un movimiento existente en la base de datos y ajusta el stock/estado.
     */
    public function update(Request $request, Movimiento $movimiento)
    {
        $validatedData = $request->validate([
            'tipo_movimiento' => 'required|string|in:entrada,salida',
            'cantidad' => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:500',
            'item_type' => 'required|string|in:insumo_medico,equipo_ti',
            'item_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('item_type') == 'insumo_medico') {
                        if (!InsumoMedico::find($value)) {
                            $fail('El insumo médico seleccionado no es válido.');
                        }
                    } elseif ($request->input('item_type') == 'equipo_ti') {
                        if (!EquipoTi::find($value)) {
                            $fail('El equipo TI seleccionado no es válido.');
                        }
                    }
                },
            ],
        ]);

        // --- Lógica para REVERTIR el cambio de stock/estado ORIGINAL ---
        if ($movimiento->insumo_medico_id) { // Era un insumo
            $insumo = InsumoMedico::find($movimiento->insumo_medico_id);
            if ($insumo) {
                if ($movimiento->tipo_movimiento == 'entrada') {
                    $insumo->stock -= $movimiento->cantidad; // Revertir entrada original
                } else { // salida
                    $insumo->stock += $movimiento->cantidad; // Revertir salida original
                }
                $insumo->save();
            }
        } elseif ($movimiento->equipo_ti_id) { // Era un equipo TI
            // Lógica para revertir cambios en equipos TI si aplica (ej. estado/ubicación)
            // Esto es más complejo y depende de tu gestión específica.
        }

        // --- Actualizar el movimiento con los nuevos datos ---
        $movimiento->tipo_movimiento = $validatedData['tipo_movimiento'];
        $movimiento->cantidad = $validatedData['cantidad'];
        $movimiento->descripcion = $validatedData['descripcion'];

        // Resetear ambas FKs a null y luego asignar la correcta para el nuevo item
        $movimiento->insumo_medico_id = null;
        $movimiento->equipo_ti_id = null;

        if ($validatedData['item_type'] == 'insumo_medico') {
            $movimiento->insumo_medico_id = $validatedData['item_id'];
        } elseif ($validatedData['item_type'] == 'equipo_ti') {
            $movimiento->equipo_ti_id = $validatedData['item_id'];
        }
        $movimiento->save();

        // --- Lógica para APLICAR el nuevo cambio de stock/estado ---
        if ($validatedData['item_type'] == 'insumo_medico') {
            $insumo = InsumoMedico::find($validatedData['item_id']);
            if ($insumo) {
                if ($validatedData['tipo_movimiento'] == 'entrada') {
                    $insumo->stock += $validatedData['cantidad'];
                } else { // salida
                    $insumo->stock -= $validatedData['cantidad'];
                }
                $insumo->save();
            }
        } elseif ($validatedData['item_type'] == 'equipo_ti') {
            // Lógica para aplicar el nuevo cambio de estado/ubicación para el equipo TI
        }

        return redirect()->route('movimientos.index')->with('success', 'Movimiento actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     * Elimina un movimiento de la base de datos y revierte el stock/estado.
     */
    public function destroy(Movimiento $movimiento)
    {
        // Lógica para REVERTIR el stock/estado antes de eliminar el movimiento
        if ($movimiento->insumo_medico_id) { // Si era un insumo
            $insumo = InsumoMedico::find($movimiento->insumo_medico_id);
            if ($insumo) {
                if ($movimiento->tipo_movimiento == 'entrada') {
                    $insumo->stock -= $movimiento->cantidad; // Si fue entrada, restar stock
                } else { // salida
                    $insumo->stock += $movimiento->cantidad; // Si fue salida, sumar stock de nuevo
                }
                $insumo->save();
            }
        } elseif ($movimiento->equipo_ti_id) { // Si era un equipo TI
            // Lógica para revertir cambios en equipos TI (ej. estado, ubicación)
            // Esto es altamente dependiente de cómo gestionas los equipos.
        }

        $movimiento->delete();
        return redirect()->route('movimientos.index')->with('success', 'Movimiento eliminado exitosamente.');
    }
}