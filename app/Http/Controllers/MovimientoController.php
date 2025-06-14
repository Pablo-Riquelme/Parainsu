<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\InsumoMedico;
use App\Models\EquipoTi;
use App\Models\User; // Asegúrate de importar el modelo User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon; // Para trabajar con fechas en los filtros

class MovimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra una lista de todos los movimientos con filtros y paginación.
     */
    public function index(Request $request)
    {
        $query = Movimiento::with(['user', 'equipoTi', 'insumoMedico'])
                           ->orderBy('created_at', 'desc');

        // Lógica de filtrado por rol: Si el usuario tiene el rol 'bodega',
        // solo puede ver movimientos relacionados con insumos médicos.
        $user = Auth::user();
        // Asegúrate que el método isBodega() exista en tu modelo User
        if ($user && method_exists($user, 'isBodega') && $user->isBodega()) {
            $query->whereNotNull('insumo_medico_id'); // Solo movimientos de insumos médicos
        }

        // --- Filtros basados en la solicitud (Request) ---

        // Filtro por tipo de movimiento (columna 'tipo')
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        // Filtro por usuario que realizó el movimiento
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filtro por ID de equipo TI (si seleccionan un equipo específico)
        if ($request->filled('equipo_ti_id')) {
            $query->where('equipo_ti_id', $request->input('equipo_ti_id'));
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_inicio')) {
            $query->where('created_at', '>=', Carbon::parse($request->input('fecha_inicio'))->startOfDay());
        }
        if ($request->filled('fecha_fin')) {
            $query->where('created_at', '<=', Carbon::parse($request->input('fecha_fin'))->endOfDay());
        }

        $movimientos = $query->paginate(15)->withQueryString(); // withQueryString() mantiene los filtros en la paginación

        // --- Datos para los filtros en la vista ---
        // Obtener tipos de movimiento únicos de la base de datos
        $tiposMovimiento = Movimiento::select('tipo')->distinct()->pluck('tipo');
        // Obtener todos los usuarios (para el dropdown de filtro)
        $usuarios = User::orderBy('name')->get();
        // Obtener todos los equipos TI (para el dropdown de filtro)
        $equipos = EquipoTi::orderBy('nombre_equipo')->get(); // Asegúrate que 'nombre_equipo' es el campo correcto

        // Pasamos todas las variables necesarias a la vista
        return view('movimientos.index', compact('movimientos', 'tiposMovimiento', 'usuarios', 'equipos'));
    }

    /**
     * Show the form for creating a new resource.
     * Muestra el formulario para crear un nuevo movimiento.
     */
    public function create()
    {
        $insumos = InsumoMedico::orderBy('nombre')->get();
        $equipos = EquipoTi::orderBy('nombre_equipo')->get();
        return view('movimientos.create', compact('insumos', 'equipos'));
    }

    /**
     * Store a newly created resource in storage.
     * Guarda un nuevo movimiento en la base de datos y actualiza el stock/estado.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tipo' => 'required|string|in:entrada,salida',
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

        $movimiento = new Movimiento();
        $movimiento->tipo = $validatedData['tipo'];
        $movimiento->cantidad = $validatedData['cantidad'];
        $movimiento->descripcion = $validatedData['descripcion'];
        $movimiento->user_id = auth()->id();
        $movimiento->ip_address = $request->ip(); // Registrar la IP

        if ($validatedData['item_type'] == 'insumo_medico') {
            $movimiento->insumo_medico_id = $validatedData['item_id'];
            $movimiento->equipo_ti_id = null;
            $movimiento->tabla_afectada = 'insumos_medicos'; // Registrar la tabla afectada
            $movimiento->id_afectada = $validatedData['item_id']; // Registrar el ID afectado
        } elseif ($validatedData['item_type'] == 'equipo_ti') {
            $movimiento->equipo_ti_id = $validatedData['item_id'];
            $movimiento->insumo_medico_id = null;
            $movimiento->tabla_afectada = 'equipos_ti'; // Registrar la tabla afectada
            $movimiento->id_afectada = $validatedData['item_id']; // Registrar el ID afectado
        }

        // Si es un movimiento de tipo 'entrada' o 'salida', guardamos los datos antes y despues del stock
        if (in_array($movimiento->tipo, ['entrada', 'salida'])) {
            $item = null;
            if ($movimiento->insumo_medico_id) {
                $item = InsumoMedico::find($movimiento->insumo_medico_id);
                $oldStock = $item->stock;
                if ($movimiento->tipo == 'entrada') {
                    $newStock = $oldStock + $movimiento->cantidad;
                } else { // salida
                    $newStock = $oldStock - $movimiento->cantidad;
                }
                $movimiento->datos_antes = json_encode(['stock' => $oldStock]);
                $movimiento->datos_despues = json_encode(['stock' => $newStock]);
            }
            // Para equipos TI de tipo 'entrada'/'salida' (si se implementa como stock)
            // se haría una lógica similar, pero para equipos TI, normalmente
            // no hay "stock" sino "estado" o "ubicación".
            // Para esto ya tenemos la lógica en EquipoTIController@update.
        }


        $movimiento->save();

        // Actualizar stock de insumos
        if ($validatedData['item_type'] == 'insumo_medico') {
            $insumo = InsumoMedico::find($validatedData['item_id']);
            if ($insumo) {
                if ($validatedData['tipo'] == 'entrada') {
                    $insumo->stock += $validatedData['cantidad'];
                } else { // salida
                    // Asegurar que no se reste más de lo que hay en stock
                    if ($insumo->stock < $validatedData['cantidad']) {
                        return redirect()->back()->withErrors(['cantidad' => 'No hay suficiente stock para esta salida.'])->withInput();
                    }
                    $insumo->stock -= $validatedData['cantidad'];
                }
                $insumo->save();
            }
        } elseif ($validatedData['item_type'] == 'equipo_ti') {
            // Lógica para equipos TI (si aplica para movimientos tipo entrada/salida)
            // Si un equipo TI se "da de alta" (entrada) o "de baja" (salida) por este controlador,
            // aquí se podría cambiar su estado (ej. de 'en_desuso' a 'en_uso').
            // Por ahora, solo se registran los movimientos de edición y baja de EquipoTI
            // en EquipoTIController, no en este store.
        }

        return redirect()->route('movimientos.index')->with('success', 'Movimiento registrado exitosamente.');
    }

    /**
     * Display the specified resource.
     * Muestra los detalles de un movimiento específico.
     */
    public function show(Movimiento $movimiento)
    {
        // Cargar relaciones para la vista de detalle
        $movimiento->load(['user', 'equipoTi', 'insumoMedico']);
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
            'tipo' => 'required|string|in:entrada,salida',
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
        // Se revierte el impacto del movimiento original antes de aplicar el nuevo.
        if ($movimiento->insumo_medico_id) {
            $insumo = InsumoMedico::find($movimiento->insumo_medico_id);
            if ($insumo) {
                if ($movimiento->tipo == 'entrada') {
                    $insumo->stock -= $movimiento->cantidad;
                } else { // salida
                    $insumo->stock += $movimiento->cantidad;
                }
                $insumo->save();
            }
        } elseif ($movimiento->equipo_ti_id) {
            // Lógica para revertir cambios en equipos TI si se registran aquí
            // (Actualmente, los cambios de equipo se manejan en EquipoTIController)
        }

        // --- Actualizar el movimiento con los nuevos datos ---
        $oldDatosAntes = $movimiento->datos_antes; // Capturar para datos_antes del nuevo movimiento si se cambia el item
        $oldDatosDespues = $movimiento->datos_despues; // Capturar para datos_despues del nuevo movimiento

        $movimiento->tipo = $validatedData['tipo'];
        $movimiento->cantidad = $validatedData['cantidad'];
        $movimiento->descripcion = $validatedData['descripcion'];
        $movimiento->ip_address = $request->ip(); // Actualizar IP

        // Resetear IDs para asignar el nuevo item
        $movimiento->insumo_medico_id = null;
        $movimiento->equipo_ti_id = null;
        $movimiento->tabla_afectada = null;
        $movimiento->id_afectada = null;

        if ($validatedData['item_type'] == 'insumo_medico') {
            $movimiento->insumo_medico_id = $validatedData['item_id'];
            $movimiento->tabla_afectada = 'insumos_medicos';
            $movimiento->id_afectada = $validatedData['item_id'];
        } elseif ($validatedData['item_type'] == 'equipo_ti') {
            $movimiento->equipo_ti_id = $validatedData['item_id'];
            $movimiento->tabla_afectada = 'equipos_ti';
            $movimiento->id_afectada = $validatedData['item_id'];
        }

        // Recalcular y guardar datos_antes y datos_despues para el movimiento actualizado
        if (in_array($movimiento->tipo, ['entrada', 'salida'])) {
            $item = null;
            if ($movimiento->insumo_medico_id) {
                $item = InsumoMedico::find($movimiento->insumo_medico_id);
                // Si el item_id no cambió, podemos usar los oldDatosDespues como oldDatosAntes
                // de la modificación actual, si no, obtenemos el stock actual del item
                $currentStock = $item->stock;
                $movimiento->datos_antes = json_encode(['stock' => $currentStock]); // Stock actual antes de aplicar el nuevo movimiento

                if ($movimiento->tipo == 'entrada') {
                    $newStock = $currentStock + $movimiento->cantidad;
                } else { // salida
                    $newStock = $currentStock - $movimiento->cantidad;
                }
                $movimiento->datos_despues = json_encode(['stock' => $newStock]);
            }
        }


        $movimiento->save();

        // --- Lógica para APLICAR el nuevo cambio de stock/estado ---
        // Se aplica el impacto del movimiento con los nuevos datos.
        if ($validatedData['item_type'] == 'insumo_medico') {
            $insumo = InsumoMedico::find($validatedData['item_id']);
            if ($insumo) {
                if ($validatedData['tipo'] == 'entrada') {
                    $insumo->stock += $validatedData['cantidad'];
                } else { // salida
                    // Asegurar que no se reste más de lo que hay en stock para la nueva operación
                    if ($insumo->stock < $validatedData['cantidad']) {
                        // Podrías revertir el movimiento y mostrar un error, o manejarlo de otra forma
                        return redirect()->back()->withErrors(['cantidad' => 'No hay suficiente stock para esta salida con los nuevos valores.'])->withInput();
                    }
                    $insumo->stock -= $validatedData['cantidad'];
                }
                $insumo->save();
            }
        } elseif ($validatedData['item_type'] == 'equipo_ti') {
            // Lógica para aplicar el nuevo cambio de estado/ubicación para el equipo TI
            // (Normalmente esto se haría si los movimientos tipo 'entrada'/'salida' afectaran directamente el estado de un equipo)
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
        if ($movimiento->insumo_medico_id) {
            $insumo = InsumoMedico::find($movimiento->insumo_medico_id);
            if ($insumo) {
                if ($movimiento->tipo == 'entrada') {
                    $insumo->stock -= $movimiento->cantidad;
                } else { // salida
                    $insumo->stock += $movimiento->cantidad;
                }
                $insumo->save();
            }
        } elseif ($movimiento->equipo_ti_id) {
            // Lógica para revertir cambios en equipos TI si se registran aquí
        }

        $movimiento->delete();
        return redirect()->route('movimientos.index')->with('success', 'Movimiento eliminado exitosamente y stock revertido.');
    }
}