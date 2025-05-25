<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTIMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica si el usuario está autenticado Y tiene el rol 'admin_ti'
        // Usamos Auth::user()->isAdmin() directamente.
        if (Auth::check() && Auth::user()->isAdmin()) {
            return $next($request);
        }

        // Si no es admin_ti, redirige al home con un mensaje de error
        return redirect('/home')->with('error', 'Acceso denegado. No tienes permisos de Administrador TI para esta sección.');
    }
}