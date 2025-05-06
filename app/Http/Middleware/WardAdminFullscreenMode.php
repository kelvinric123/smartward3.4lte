<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WardAdminFullscreenMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has ward-admin or super-admin role
        if (Auth::check() && (Auth::user()->hasRole('ward-admin') || Auth::user()->hasRole('super-admin'))) {
            // Set fullscreen mode view variable 
            view()->share('fullscreen_mode', true);
        }

        return $next($request);
    }
} 