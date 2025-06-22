<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatApiController; // Importa el nuevo controlador de la API

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar las rutas de la API para tu aplicación. Estas
| rutas son cargadas por el RouteServiceProvider y todas ellas serán
| asignadas al grupo de middleware "api". ¡Haz algo genial!
|
*/

// Ruta de ejemplo para obtener el usuario autenticado a través de Sanctum
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de la API para la funcionalidad de chat
Route::post('/chats/private', [ChatApiController::class, 'createPrivateChat']); // Para iniciar un nuevo chat privado
Route::get('/chats/{chat}/messages', [ChatApiController::class, 'getMessages']); // Para obtener los mensajes de un chat
Route::post('/chats/{chat}/messages', [ChatApiController::class, 'sendMessage']); // Para enviar un nuevo mensaje

