<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin'); // Asegúrate de tener este middleware definido
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard(): View
    {
        // Aquí puedes obtener datos específicos para el dashboard del administrador
        $userCount = \App\Models\User::count();
        $roleCount = \App\Models\Role::count();
        // ... otros datos que quieras mostrar en el dashboard

        return view('admin.dashboard', compact('userCount', 'roleCount'));
    }

    // Aquí puedes agregar otros métodos para las funcionalidades específicas del administrador TI
    // Por ejemplo:
    // - Métodos para gestionar roles y permisos (si no tienes controladores separados)
    // - Métodos para ver logs del sistema
    // - Métodos para realizar tareas de mantenimiento
}