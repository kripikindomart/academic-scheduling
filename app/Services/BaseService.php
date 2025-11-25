<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    /**
     * Get a success response with data
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function success($data = null, string $message = 'Operation successful', int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Get an error response
     *
     * @param string $message
     * @param mixed $errors
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function error(string $message = 'Operation failed', $errors = null, int $statusCode = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Get a paginated response
     *
     * @param LengthAwarePaginator $paginator
     * @param string $message
     * @return JsonResponse
     */
    protected function paginated(LengthAwarePaginator $paginator, string $message = 'Data retrieved successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ]
        ]);
    }

    /**
     * Get a not found response
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, null, 404);
    }

    /**
     * Get an unauthorized response
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->error($message, null, 401);
    }

    /**
     * Get a forbidden response
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->error($message, null, 403);
    }

    /**
     * Get a server error response
     *
     * @param string $message
     * @param \Exception|null $exception
     * @return JsonResponse
     */
    protected function serverError(string $message = 'Internal server error', ?\Exception $exception = null): JsonResponse
    {
        Log::error($message, [
            'exception' => $exception,
            'trace' => $exception?->getTraceAsString(),
        ]);

        return $this->error($message, null, 500);
    }

    /**
     * Log info message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    protected function logInfo(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }

    /**
     * Log warning message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    protected function logWarning(string $message, array $context = []): void
    {
        Log::warning($message, $context);
    }

    /**
     * Log error message
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    protected function logError(string $message, array $context = []): void
    {
        Log::error($message, $context);
    }

    /**
     * Validate data and return errors if any
     *
     * @param array $rules
     * @param array $data
     * @return array|null
     */
    protected function validate(array $rules, array $data): ?array
    {
        $validator = validator($data, $rules);

        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }

        return null;
    }

    /**
     * Transform model to array with relationships
     *
     * @param Model|Collection $model
     * @param array $relationships
     * @return array
     */
    protected function transformWithRelations($model, array $relationships = []): array
    {
        if ($model instanceof Collection) {
            return $model->map(function ($item) use ($relationships) {
                return $this->transformWithRelations($item, $relationships);
            })->toArray();
        }

        $data = $model->toArray();

        foreach ($relationships as $relation) {
            if ($model->relationLoaded($relation)) {
                $data[$relation] = $this->transformWithRelations($model->$relation);
            }
        }

        return $data;
    }

    /**
     * Format date for API response
     *
     * @param string $date
     * @param string $format
     * @return string
     */
    protected function formatDate(string $date, string $format = 'Y-m-d H:i:s'): string
    {
        return date($format, strtotime($date));
    }

    /**
     * Get current user from request
     *
     * @return \App\Models\User|null
     */
    protected function getCurrentUser(): ?\App\Models\User
    {
        return auth('sanctum')->user();
    }

    /**
     * Check if current user has specific permission
     *
     * @param string $permission
     * @return bool
     */
    protected function hasPermission(string $permission): bool
    {
        return $this->getCurrentUser()?->hasPermissionTo($permission) ?? false;
    }

    /**
     * Check if current user has specific role
     *
     * @param string $role
     * @return bool
     */
    protected function hasRole(string $role): bool
    {
        return $this->getCurrentUser()?->hasRole($role) ?? false;
    }
}