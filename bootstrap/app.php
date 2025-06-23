<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
->withMiddleware(function (Middleware $middleware) {
    // ... otras configuraciones de middleware ...

    $middleware->alias([
        'admin_ti' => \App\Http\Middleware\AdminTIMiddleware::class,
        'admin_ti_bodega' => \App\Http\Middleware\AdminTIOBodegaMiddleware::class,
        // Si tienes otros alias de middleware, agrégalos aquí también
        // 'bodega' => \App\Http\Middleware\BodegaMiddleware::class,
    ]);
})
->withExceptions(function (Exceptions $exceptions) {
    //
})->create();
    
