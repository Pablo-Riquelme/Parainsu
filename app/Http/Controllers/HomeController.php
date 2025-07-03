<?php

namespace App\Http\Controllers;

use App\Models\Chat; 
use App\Models\Message;
use App\Models\Mantenimiento; 
use App\Models\Movimiento; // ¡Importa tu modelo Movimiento!
use Illuminate\Support\Facades\Auth;
use App\Models\InsumoMedico;
use Illuminate\Support\Carbon; // Para formateo de fechas

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth'); // Asegura que el usuario esté autenticado
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Obtener los últimos mensajes del usuario autenticado
        $latestMessages = Message::whereHas('chat.users', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })
        ->with(['chat', 'user'])
        ->latest()
        ->take(5)
        ->get();

        // 2. Obtener los últimos movimientos realizados en el sistema
        // Cargamos las relaciones para poder mostrar los nombres de usuario, equipos o insumos.
        $latestChanges = Movimiento::with(['user', 'equipoTi', 'insumoMedico'])
                               ->latest() // Ordena por created_at de forma descendente
                               ->take(5) // Obtiene los 5 movimientos más recientes
                               ->get();

        $mantenimientosPendientes = collect(); 
        $insumosBajoStock = collect();

        // Solo cargar mantenimientos pendientes si el usuario es admin_ti o usuario normal
        if ($user && ($user->isAdmin() || $user->isUser())) { 
            $mantenimientosPendientes = Mantenimiento::whereIn('estado', ['pendiente', 'en_progreso'])
                                                    ->with('equipoTi')
                                                    ->orderBy('fecha_inicio', 'asc')
                                                    ->get();
        }
        // Lógica para insumos con bajo stock (visibles para admin y bodega)
        if ($user && ($user->isAdmin() || $user->isBodega() || $user->isUser())) { // Usar isAdmin() y isUser()
            $insumosBajoStock = InsumoMedico::whereColumn('stock', '<=', 'stock_minimo')
                                            ->orderBy('nombre', 'asc') // Ordenar por nombre del insumo
                                            ->get();
        }

        return view('home', compact('latestMessages', 'latestChanges', 'mantenimientosPendientes', 'insumosBajoStock'));
    }
}
