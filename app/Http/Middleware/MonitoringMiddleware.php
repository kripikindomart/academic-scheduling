<?php

namespace App\Http\Middleware;

use App\Services\MonitoringService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MonitoringMiddleware
{
    protected MonitoringService $monitoringService;
    protected float $startTime;

    public function __construct(MonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->startTime = microtime(true);

        $response = $next($request);

        $this->logRequest($request, $response);

        return $response;
    }

    /**
     * Log request and response data
     *
     * @param \Illuminate\Http\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    protected function logRequest(Request $request, Response $response): void
    {
        try {
            $executionTime = microtime(true) - $this->startTime;

            $requestData = [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'headers' => $request->headers->all(),
                'body' => $request->getContent(),
            ];

            $responseData = [
                'status' => $response->getStatusCode(),
                'headers' => $response->headers->all(),
                'body' => $response->getContent(),
            ];

            // Log API request
            $this->monitoringService->logApiRequest($requestData, $responseData, $executionTime);

            // Log performance metrics
            $this->monitoringService->logPerformance([
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'response_time' => $executionTime,
                'user_id' => auth('sanctum')->id(),
                'ip_address' => $request->ip(),
            ]);

            // Log slow requests
            if ($executionTime > 2.0) {
                $this->logSlowRequest($request, $executionTime);
            }

        } catch (\Exception $e) {
            Log::error('Error in MonitoringMiddleware: ' . $e->getMessage());
        }
    }

    /**
     * Log slow requests
     *
     * @param \Illuminate\Http\Request $request
     * @param float $executionTime
     * @return void
     */
    protected function logSlowRequest(Request $request, float $executionTime): void
    {
        $data = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'execution_time' => $executionTime,
            'user_id' => auth('sanctum')->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        Log::channel('performance')->warning('Slow request detected', $data);
    }
}