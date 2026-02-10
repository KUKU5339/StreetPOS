<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     * These endpoints are used for offline sync and may have stale tokens.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/csrf-token',
        'api/sync-sale',
        'api/sync-product',
        'logout',
    ];
}
