<?php

namespace App\Http\Middleware;

use App\Models\Traffic;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Visit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $today = today()->format('Y-m-d');

        if (! Traffic::where('tanggal', $today)->where('ip', $ip)->exists()) {
            Traffic::create([
                'tanggal' => $today,
                'ip' => $ip,
            ]);
        }

        return $next($request);
    }
}
