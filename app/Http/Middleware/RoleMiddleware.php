<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Superadmin has access to everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if ($role === 'manager') {
            if (!$user->isManager()) {
                abort(403, 'Akses ditolak. Anda bukan pengelola.');
            }
            
            if ($user->manager_status === 'pending' && !$request->routeIs('manager.waiting')) {
                return redirect()->route('manager.waiting');
            }
        }

        if ($role === 'tenant' && !$user->isTenant()) {
            abort(403, 'Akses ditolak. Anda bukan penyewa.');
        }

        return $next($request);
    }
}
