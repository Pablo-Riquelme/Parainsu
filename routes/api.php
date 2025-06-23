    <?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Api\ChatApiController; // ¡CRUCIAL! Asegúrate de que la ruta a tu controlador sea correcta

    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Estas rutas son cargadas por el RouteServiceProvider y se les asigna
    | automáticamente el grupo de middleware "api".
    |
    */

    // Ruta de prueba para verificar autenticación Sanctum
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    // Rutas para la funcionalidad de chat, todas protegidas por Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        // Ruta para crear o encontrar un chat privado
        Route::post('/chats/private', [ChatApiController::class, 'createPrivateChat']);
        // Rutas para obtener y enviar mensajes en un chat existente
        Route::get('/chats/{chat}/messages', [ChatApiController::class, 'getMessages']);
        Route::post('/chats/{chat}/messages', [ChatApiController::class, 'sendMessage']);
    });
    