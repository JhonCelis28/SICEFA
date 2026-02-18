<?php

namespace Modules\INFRASTOCK\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyAdminRole
{
    /**
     * Verifica que el usuario autenticado tenga rol de Administrador en INFRASTOCK.
     * Si no tiene el rol, lo redirige a la página de bienvenida con un mensaje de error.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('cefa.welcome');
        }

        $user = auth()->user();
        $userRoles = $user->roles->pluck('name')->toArray();
        $userSlugs = $user->roles->pluck('slug')->toArray();

        $isAdmin = in_array('Administrador', $userRoles) 
                || in_array('Super Administrador', $userRoles)
                || in_array('infrastock.admin', $userSlugs)
                || in_array('superadmin', $userSlugs);

        if (!$isAdmin) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'No tienes permiso para acceder a esta sección. Solo administradores de INFRASTOCK pueden acceder.'
                ], 403);
            }

            abort(403, 'No tienes permiso para acceder a esta sección. Solo administradores de INFRASTOCK pueden acceder.');
        }

        return $next($request);
    }
}
