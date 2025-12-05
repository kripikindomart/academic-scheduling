<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HandleApiPermissionErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (\Spatie\Permission\Exceptions\UnauthorizedException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Access forbidden. You do not have the required permission.',
                'data' => null,
                'errors' => [
                    'permission' => [
                        'You do not have permission to access this resource. Required permission: ' . $e->getMessage()
                    ]
                ]
            ], 403);
        }
    }
}