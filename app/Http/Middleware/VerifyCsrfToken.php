<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/students/*/create-user-account',
        'api/students/bulk-create-user-accounts',
        'api/lecturers/*/create-user-account',
        'api/lecturers/bulk-create-user-accounts',
        'api/*',
    ];
}
