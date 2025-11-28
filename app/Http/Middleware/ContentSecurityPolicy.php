<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Disable CSP in development to allow Vite dev server
        if (app()->environment('local', 'testing')) {
            // In development, only add essential security headers
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
            return $response;
        }

        // Production CSP - more restrictive
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' *.googleapis.com cdnjs.cloudflare.com unpkg.com",
            "style-src 'self' 'unsafe-inline' *.googleapis.com cdnjs.cloudflare.com fonts.googleapis.com fonts.bunny.net",
            "img-src 'self' data: blob: *.googleusercontent.com",
            "font-src 'self' fonts.gstatic.com fonts.googleapis.com cdnjs.cloudflare.com fonts.bunny.net",
            "connect-src 'self' ws: wss:",
            "object-src 'none'",
            "media-src 'self'",
            "frame-src 'none'",
            "child-src 'none'",
            "worker-src 'self' blob:",
            "form-action 'self'",
            "base-uri 'self'",
            "manifest-src 'self'",
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $csp));
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        return $response;
    }
}