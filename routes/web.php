<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EquipoTIController;
use App\Http\Controllers\InsumoMedicoController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Rutas accesibles para usuarios autenticados (middleware 'auth' implícito o explícito si se necesita)
// Las rutas que estaban aquí fuera de un grupo auth explícito, Laravel las asume protegidas por 'web' y 'auth'
// si están en un proyecto con autenticación scaffolding.
// Sin embargo, para mayor claridad y control, es mejor agruparlas.

// Agrupamos las rutas que requieren autenticación.
// Dentro de este grupo, anidaremos las que requieren el rol 'admin_ti'.
Route::middleware(['auth'])->group(function () {

    // Ruta del home/dashboard principal para usuarios autenticados
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile.show');
    // Dashboard específico de usuario
    Route::get('/user/dashboard', [UserController::class, 'show'])->name('user.dashboard');

    // Ruta de logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // =========================================================================
    // RUTAS PROTEGIDAS PARA EL ROL 'admin_ti'
    // Estas rutas solo serán accesibles si el usuario logueado tiene el rol 'admin_ti'.
    // El alias 'admin_ti' debe estar registrado en bootstrap/app.php.
    // =========================================================================
    Route::middleware(['admin_ti'])->group(function () {

        // Dashboard de Administrador
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

        // Rutas de Gestión de Usuarios (todas las acciones CRUD)
        // Las rutas individuales que tenías se consolidan en Route::resource
        // para aplicar el middleware de forma más eficiente.
        // Si necesitas rutas adicionales no cubiertas por resource, añádelas aquí.
        Route::resource('users', UserController::class);

        // Rutas de Gestión de Equipos TI (todas las acciones CRUD)
        Route::resource('equipos-ti', EquipoTIController::class)->parameters([
            'equipos-ti' => 'equipoTI',
        ]);

        // Rutas de Gestión de Roles
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        // Si hay más acciones CRUD para roles, considera Route::resource('roles', RoleController::class);
    });
    // =========================================================================
    // RUTAS PROTEGIDAS PARA ROLES 'admin_ti' O 'bodega'
    // =========================================================================
    Route::middleware(['admin_ti_bodega'])->group(function () {
        Route::resource('insumos-medicos', InsumoMedicoController::class)->parameters([
            'insumos-medicos' => 'insumo_medico',
]);
    });

    // Aquí irían otras rutas que requieran autenticación pero no el rol 'admin_ti'
    // Por ejemplo, si tuvieras rutas para el rol 'bodega' o 'usuario_normal'
    // Route::middleware(['bodega'])->group(function () { ... });
    // Route::middleware(['usuario_normal'])->group(function () { ... });

});