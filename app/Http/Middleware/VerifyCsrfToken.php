<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'admin/integration/*',  // Exclude HL7 integration endpoints from CSRF
        'admin/integration/admission',  // Specifically for HL7 ADT integrator
    ];
}
