<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     * Only allow users with role 'admin' to proceed.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isTeacher()) {
            return redirect()->route('marks.index')
                ->with('error', 'You do not have permission to access that page.');
        }

        return $next($request);
    }
}
