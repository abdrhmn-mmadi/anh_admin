<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ManagerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role?->name !== 'Manager') {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
