<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureManagerAcceptedTerms
{
    /**
     * Redirect manager to T&C page if they haven't accepted yet.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->role === 'manager' && is_null($user->terms_accepted_at)) {
            // Allow the terms page and waiting page only
            if (!$request->routeIs('manager.terms.*') && !$request->routeIs('manager.waiting')) {
                return redirect()->route('manager.terms.show');
            }
        }

        return $next($request);
    }
}
