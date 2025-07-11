<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Response;

class ConfigurePagination
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set Bootstrap 4 as the default pagination view
        Paginator::defaultView('vendor.pagination.bootstrap-4');
        
        // Set simple-bootstrap-4 as the default tailwind view (for simple pagination)
        Paginator::defaultSimpleView('vendor.pagination.simple-bootstrap-4');
        
        return $next($request);
    }
}
