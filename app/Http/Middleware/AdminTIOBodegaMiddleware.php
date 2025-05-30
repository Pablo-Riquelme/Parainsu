<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTIOBodegaMiddleware
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
        // Verifica si el usuario está autenticado Y tiene el rol 'admin_ti' O 'bodega'
        if (Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isBodega())) {
            return $next($request);
        }

        // Si no tiene ninguno de los roles, redirige o aborta
        return redirect('/home')->with('error', 'Acceso denegado. No tienes permisos para acceder a esta sección.');
    }
}