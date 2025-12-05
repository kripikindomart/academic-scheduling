<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiAuthenticate
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
        // For API routes, we want to return JSON responses for authentication errors
        if (!$request->user('sanctum')) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
                'data' => null,
                'errors' => [
                    'authentication' => ['Invalid or missing authentication token. Please include a valid Bearer token in the Authorization header.']
                ]
            ], 401);
        }

        return $next($request);
    }
}