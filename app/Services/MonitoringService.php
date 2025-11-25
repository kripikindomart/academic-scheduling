<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitoringService extends BaseService
{
    /**
     * Log system performance metrics
     *
     * @param array $metrics
     * @return void
     */
    public function logPerformance(array $metrics): void
    {
        $logData = [
            'timestamp' => now()->toISOString(),
            'memory_usage' => $this->getMemoryUsage(),
            'cpu_usage' => $this->getCpuUsage(),
            'request_count' => $metrics['request_count'] ?? 0,
            'response_time' => $metrics['response_time'] ?? 0,
            'endpoint' => $metrics['endpoint'] ?? 'unknown',
            'method' => $metrics['method'] ?? 'unknown',
            'user_id' => $metrics['user_id'] ?? null,
            'ip_address' => $metrics['ip_address'] ?? request()->ip(),
        ];

        Log::channel('performance')->info('Performance metrics', $logData);
    }

    /**
     * Log user activity
     *
     * @param array $data
     * @return void
     */
    public function logUserActivity(array $data): void
    {
        $logData = [
            'timestamp' => now()->toISOString(),
            'user_id' => $data['user_id'],
            'user_email' => $data['user_email'] ?? null,
            'action' => $data['action'],
            'resource' => $data['resource'] ?? null,
            'resource_id' => $data['resource_id'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $data['metadata'] ?? [],
        ];

        Log::channel('activity')->info('User activity', $logData);

        // Store in cache for real-time monitoring
        $this->cacheActivity($logData);
    }

    /**
     * Log system errors
     *
     * @param \Exception $exception
     * @param array $context
     * @return void
     */
    public function logError(\Exception $exception, array $context = []): void
    {
        $logData = [
            'timestamp' => now()->toISOString(),
            'exception_class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'context' => $context,
            'user_id' => auth('sanctum')->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        Log::channel('errors')->error('Application error', $logData);

        // Cache for immediate alerting
        $this->cacheError($logData);
    }

    /**
     * Log database operations
     *
     * @param string $operation
     * @param string $table
     * @param array $data
     * @param float $executionTime
     * @return void
     */
    public function logDatabaseOperation(string $operation, string $table, array $data = [], float $executionTime = 0): void
    {
        $logData = [
            'timestamp' => now()->toISOString(),
            'operation' => $operation,
            'table' => $table,
            'data' => $data,
            'execution_time' => $executionTime,
            'user_id' => auth('sanctum')->id(),
            'memory_usage' => $this->getMemoryUsage(),
        ];

        Log::channel('database')->info('Database operation', $logData);
    }

    /**
     * Log API requests and responses
     *
     * @param array $request
     * @param array $response
     * @param float $executionTime
     * @return void
     */
    public function logApiRequest(array $request, array $response, float $executionTime): void
    {
        $logData = [
            'timestamp' => now()->toISOString(),
            'request' => [
                'method' => $request['method'],
                'url' => $request['url'],
                'headers' => $this->sanitizeHeaders($request['headers'] ?? []),
                'body' => $this->sanitizeRequestBody($request['body'] ?? []),
            ],
            'response' => [
                'status' => $response['status'],
                'size' => strlen(json_encode($response['body'] ?? [])),
            ],
            'execution_time' => $executionTime,
            'user_id' => auth('sanctum')->id(),
            'ip_address' => request()->ip(),
        ];

        Log::channel('api')->info('API request', $logData);

        // Update performance metrics
        $this->updatePerformanceMetrics($logData);
    }

    /**
     * Get system health status
     *
     * @return array
     */
    public function getSystemHealth(): array
    {
        return [
            'status' => $this->getHealthStatus(),
            'database' => $this->checkDatabaseHealth(),
            'cache' => $this->checkCacheHealth(),
            'storage' => $this->checkStorageHealth(),
            'memory' => $this->getMemoryUsage(),
            'cpu' => $this->getCpuUsage(),
            'disk_space' => $this->getDiskSpace(),
            'uptime' => $this->getSystemUptime(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get application statistics
     *
     * @return array
     */
    public function getApplicationStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'users' => [
                'total' => DB::table('users')->count(),
                'active_today' => DB::table('users')->whereDate('last_login_at', $today)->count(),
                'new_this_month' => DB::table('users')->where('created_at', '>=', $thisMonth)->count(),
            ],
            'api_calls' => $this->getApiCallStats(),
            'errors' => $this->getErrorStats(),
            'performance' => $this->getPerformanceStats(),
        ];
    }

    /**
     * Get memory usage in MB
     *
     * @return float
     */
    private function getMemoryUsage(): float
    {
        return round(memory_get_usage(true) / 1024 / 1024, 2);
    }

    /**
     * Get CPU usage percentage
     *
     * @return float
     */
    private function getCpuUsage(): float
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return round($load[0] * 100, 2);
        }

        return 0.0;
    }

    /**
     * Get disk space information
     *
     * @return array
     */
    private function getDiskSpace(): array
    {
        $total = disk_total_space('/');
        $free = disk_free_space('/');
        $used = $total - $free;

        return [
            'total' => round($total / 1024 / 1024 / 1024, 2),
            'used' => round($used / 1024 / 1024 / 1024, 2),
            'free' => round($free / 1024 / 1024 / 1024, 2),
            'percentage_used' => round(($used / $total) * 100, 2),
        ];
    }

    /**
     * Get system uptime
     *
     * @return string
     */
    private function getSystemUptime(): string
    {
        if (function_exists('shell_exec')) {
            $uptime = shell_exec('uptime -p');
            return trim($uptime ?: 'Unknown');
        }

        return 'Unknown';
    }

    /**
     * Check database health
     *
     * @return bool
     */
    private function checkDatabaseHealth(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check cache health
     *
     * @return bool
     */
    private function checkCacheHealth(): bool
    {
        try {
            Cache::put('health_check', 'ok', 60);
            return Cache::get('health_check') === 'ok';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check storage health
     *
     * @return bool
     */
    private function checkStorageHealth(): bool
    {
        return is_writable(storage_path());
    }

    /**
     * Get overall health status
     *
     * @return string
     */
    private function getHealthStatus(): string
    {
        $issues = [];

        if (!$this->checkDatabaseHealth()) $issues[] = 'database';
        if (!$this->checkCacheHealth()) $issues[] = 'cache';
        if (!$this->checkStorageHealth()) $issues[] = 'storage';

        if (empty($issues)) {
            return 'healthy';
        } elseif (count($issues) === 1) {
            return 'degraded';
        } else {
            return 'critical';
        }
    }

    /**
     * Cache activity for real-time monitoring
     *
     * @param array $data
     * @return void
     */
    private function cacheActivity(array $data): void
    {
        $key = "activity_{$data['user_id']}_" . time();
        Cache::put($key, $data, 3600); // 1 hour
    }

    /**
     * Cache error for immediate alerting
     *
     * @param array $data
     * @return void
     */
    private function cacheError(array $data): void
    {
        $key = 'error_' . time() . '_' . md5($data['message']);
        Cache::put($key, $data, 3600); // 1 hour

        // Also increment error counter
        $counter = Cache::get('error_counter', 0);
        Cache::put('error_counter', $counter + 1, 3600);
    }

    /**
     * Update performance metrics
     *
     * @param array $logData
     * @return void
     */
    private function updatePerformanceMetrics(array $logData): void
    {
        $date = date('Y-m-d');
        $key = "performance_{$date}";

        $metrics = Cache::get($key, [
            'total_requests' => 0,
            'total_response_time' => 0,
            'avg_response_time' => 0,
            'slow_requests' => 0,
            'errors' => 0,
        ]);

        $metrics['total_requests']++;
        $metrics['total_response_time'] += $logData['execution_time'];
        $metrics['avg_response_time'] = round($metrics['total_response_time'] / $metrics['total_requests'], 2);

        if ($logData['execution_time'] > 2.0) { // Slow requests (> 2 seconds)
            $metrics['slow_requests']++;
        }

        if ($logData['response']['status'] >= 400) {
            $metrics['errors']++;
        }

        Cache::put($key, $metrics, 86400); // 24 hours
    }

    /**
     * Get API call statistics
     *
     * @return array
     */
    private function getApiCallStats(): array
    {
        $today = date('Y-m-d');
        $metrics = Cache::get("performance_{$today}, []);

        return [
            'total_requests' => $metrics['total_requests'] ?? 0,
            'avg_response_time' => $metrics['avg_response_time'] ?? 0,
            'slow_requests' => $metrics['slow_requests'] ?? 0,
            'errors' => $metrics['errors'] ?? 0,
        ];
    }

    /**
     * Get error statistics
     *
     * @return array
     */
    private function getErrorStats(): array
    {
        return [
            'total_today' => Cache::get('error_counter', 0),
            'recent_errors' => $this->getRecentErrors(),
        ];
    }

    /**
     * Get recent errors
     *
     * @return array
     */
    private function getRecentErrors(): array
    {
        // This would typically query a dedicated error logs table
        // For now, return empty array
        return [];
    }

    /**
     * Get performance statistics
     *
     * @return array
     */
    private function getPerformanceStats(): array
    {
        $today = date('Y-m-d');
        $metrics = Cache::get("performance_{$today}, []);

        return [
            'avg_response_time' => $metrics['avg_response_time'] ?? 0,
            'slow_requests_count' => $metrics['slow_requests'] ?? 0,
            'total_requests' => $metrics['total_requests'] ?? 0,
        ];
    }

    /**
     * Sanitize request headers for logging
     *
     * @param array $headers
     * @return array
     */
    private function sanitizeHeaders(array $headers): array
    {
        $sensitive = ['authorization', 'cookie', 'password', 'token'];

        return collect($headers)->map(function ($value, $key) use ($sensitive) {
            if (in_array(strtolower($key), $sensitive)) {
                return '[HIDDEN]';
            }
            return $value;
        })->toArray();
    }

    /**
     * Sanitize request body for logging
     *
     * @param array $body
     * @return array
     */
    private function sanitizeRequestBody(array $body): array
    {
        $sensitive = ['password', 'password_confirmation', 'token'];

        return collect($body)->map(function ($value, $key) use ($sensitive) {
            if (in_array($key, $sensitive)) {
                return '[HIDDEN]';
            }
            return $value;
        })->toArray();
    }
}