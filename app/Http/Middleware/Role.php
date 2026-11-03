<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Support both pipe and comma separated roles
        $allowedRoles = [];
        foreach ($roles as $roleGroup) {
            foreach (explode('|', $roleGroup) as $r) {
                $allowedRoles[] = trim($r);
            }
        }

        foreach ($allowedRoles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        return redirect()->route('login');
    }
}
