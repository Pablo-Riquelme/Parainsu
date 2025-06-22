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
use App\Http\Controllers\ChatController; // Importar ChatController (para index de chats)

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

// Rutas de autenticación predeterminadas de Laravel
Auth::routes();

// Agrupamos todas las rutas que requieren que el usuario esté autenticado.
Route::middleware(['auth'])->group(function () {

    // =========================================================================
    // RUTAS ACCESIBLES PARA TODOS LOS ROLES AUTENTICADOS
    // =========================================================================

    // Ruta del home/dashboard principal para usuarios autenticados
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile.show');
    Route::get('/user/dashboard', [UserController::class, 'show'])->name('user.dashboard');

    // Ruta de logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // =========================================================================
    // RUTAS DEL SISTEMA DE CHAT (Vistas Blade)
    // =========================================================================
    // Ruta para la lista de chats del usuario
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');

    // Ruta para mostrar una conversación de chat específica
    // Usa un closure para la validación de acceso al chat.
    Route::get('/chat/{chat}', function (App\Models\Chat $chat) {
        if (!Auth::user()->chats->contains($chat->id)) {
            abort(403, 'No tienes acceso a este chat.');
        }
        return view('chat.show', compact('chat'));
    })->name('chat.show');


    // =========================================================================
    // VER MOVIMIENTOS - Accesible por TODOS los usuarios autenticados
    // =========================================================================
    Route::resource('movimientos', MovimientoController::class)->only(['index', 'show']);

    // =========================================================================
    // RUTAS PROTEGIDAS PARA EL ROL 'admin_ti'
    // =========================================================================
    Route::middleware(['admin_ti'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::resource('users', UserController::class);
        Route::resource('equipos-ti', EquipoTIController::class)->parameters([
            'equipos-ti' => 'equipoTI',
        ]);
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    });

    // =========================================================================
    // RUTAS PROTEGIDAS PARA ROLES 'admin_ti' O 'bodega'
    // =========================================================================
    Route::middleware(['admin_ti_bodega'])->group(function () {
        Route::resource('insumos-medicos', InsumoMedicoController::class)->parameters([
            'insumos-medicos' => 'insumo_medico',
        ]);
    });

}); // Cierre del grupo de middleware 'auth'
