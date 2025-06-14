<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EquipoTIController;
use App\Http\Controllers\InsumoMedicoController;
use App\Http\Controllers\MovimientoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación predeterminadas de Laravel (login, register, reset password, etc.)
Auth::routes();

// Agrupamos todas las rutas que requieren que el usuario esté autenticado.
Route::middleware(['auth'])->group(function () {

    // =========================================================================
    // RUTAS ACCESIBLES PARA TODOS LOS ROLES AUTENTICADOS
    // (Middleware 'auth' es suficiente aquí)
    // =========================================================================

    // Ruta del home/dashboard principal para usuarios autenticados (accesible por todos)
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile.show');
    // Dashboard específico de usuario (si lo usas como un dashboard general para usuarios comunes)
    Route::get('/user/dashboard', [UserController::class, 'show'])->name('user.dashboard');

    // Ruta de logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // =========================================================================
    // VER MOVIMIENTOS - Accesible por TODOS los usuarios autenticados
    // Se mueve fuera de los grupos de rol específicos.
    // Solo necesitamos las acciones 'index' (lista) y 'show' (detalle) para movimientos.
    // =========================================================================
    Route::resource('movimientos', MovimientoController::class)->only(['index', 'show']);

    // =========================================================================
    // RUTAS PROTEGIDAS PARA EL ROL 'admin_ti'
    // Estas rutas solo serán accesibles si el usuario logueado tiene el rol 'admin_ti'.
    // Asegúrate de que el middleware 'admin_ti' esté correctamente registrado.
    // =========================================================================
    Route::middleware(['admin_ti'])->group(function () {

        // Dashboard de Administrador
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

        // Rutas de Gestión de Usuarios (CRUD completo)
        Route::resource('users', UserController::class);

        // Rutas de Gestión de Equipos TI (CRUD completo)
        Route::resource('equipos-ti', EquipoTIController::class)->parameters([
            'equipos-ti' => 'equipoTI', // Asegura que el Route Model Binding use 'equipoTI'
        ]);

        // Rutas de Gestión de Roles (asumiendo que 'index' es el único por ahora)
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        // Si hay más acciones CRUD para roles, puedes cambiar a Route::resource('roles', RoleController::class);

        // Puedes añadir aquí rutas para "Gestionar Permisos" si las implementas más adelante
        // Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    });

    // =========================================================================
    // RUTAS PROTEGIDAS PARA ROLES 'admin_ti' O 'bodega'
    // Asegúrate de que el middleware 'admin_ti_bodega' esté correctamente registrado.
    // =========================================================================
    Route::middleware(['admin_ti_bodega'])->group(function () {
        // Rutas de Gestión de Insumos Médicos (CRUD completo)
        Route::resource('insumos-medicos', InsumoMedicoController::class)->parameters([
            'insumos-medicos' => 'insumo_medico', // Asegura que el Route Model Binding use 'insumo_medico'
        ]);

        // Puedes añadir aquí otras rutas que sean exclusivas para admin_ti O bodega
        // Por ejemplo, rutas para "Ver Inventario", "Gestionar Entradas", "Gestionar Salidas", "Gestionar Proveedores",
        // o "Dashboard Bodega" si son funcionalidades separadas que requieren este middleware.
    });

}); // Cierre del grupo de middleware 'auth'