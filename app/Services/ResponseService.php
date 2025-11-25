<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class ResponseService extends BaseService
{
    /**
     * Create standardized API response
     *
     * @param bool $success
     * @param string $message
     * @param mixed $data
     * @param int $code
     * @param mixed $meta
     * @return JsonResponse
     */
    public static function create(bool $success, string $message, $data = null, int $code = 200, $meta = null): JsonResponse
    {
        $response = [
            'success' => $success,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($meta !== null) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Success response
     *
     * @param mixed $data
     * @param string $message
     * @param mixed $meta
     * @return JsonResponse
     */
    public static function success($data = null, string $message = 'Operation successful', $meta = null): JsonResponse
    {
        return self::create(true, $message, $data, 200, $meta);
    }

    /**
     * Created response
     *
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    public static function created($data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return self::create(true, $message, $data, 201);
    }

    /**
     * Accepted response
     *
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    public static function accepted($data = null, string $message = 'Request accepted'): JsonResponse
    {
        return self::create(true, $message, $data, 202);
    }

    /**
     * No content response
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function noContent(string $message = 'Operation successful'): JsonResponse
    {
        return self::create(true, $message, null, 204);
    }

    /**
     * Bad request response
     *
     * @param string $message
     * @param mixed $errors
     * @return JsonResponse
     */
    public static function badRequest(string $message = 'Bad request', $errors = null): JsonResponse
    {
        return self::create(false, $message, null, 400, $errors);
    }

    /**
     * Unauthorized response
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::create(false, $message, null, 401);
    }

    /**
     * Forbidden response
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return self::create(false, $message, null, 403);
    }

    /**
     * Not found response
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::create(false, $message, null, 404);
    }

    /**
     * Method not allowed response
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function methodNotAllowed(string $message = 'Method not allowed'): JsonResponse
    {
        return self::create(false, $message, null, 405);
    }

    /**
     * Conflict response
     *
     * @param string $message
     * @param mixed $errors
     * @return JsonResponse
     */
    public static function conflict(string $message = 'Conflict', $errors = null): JsonResponse
    {
        return self::create(false, $message, null, 409, $errors);
    }

    /**
     * Validation error response
     *
     * @param mixed $errors
     * @param string $message
     * @return JsonResponse
     */
    public static function validationError($errors, string $message = 'Validation failed'): JsonResponse
    {
        return self::create(false, $message, null, 422, $errors);
    }

    /**
     * Too many requests response
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function tooManyRequests(string $message = 'Too many requests'): JsonResponse
    {
        return self::create(false, $message, null, 429);
    }

    /**
     * Internal server error response
     *
     * @param string $message
     * @param mixed $errors
     * @return JsonResponse
     */
    public static function internalServerError(string $message = 'Internal server error', $errors = null): JsonResponse
    {
        return self::create(false, $message, null, 500, $errors);
    }

    /**
     * Service unavailable response
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function serviceUnavailable(string $message = 'Service unavailable'): JsonResponse
    {
        return self::create(false, $message, null, 503);
    }

    /**
     * Paginated response
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $paginator
     * @param string $message
     * @param mixed $meta
     * @return JsonResponse
     */
    public static function paginated($paginator, string $message = 'Data retrieved successfully', $meta = null): JsonResponse
    {
        $pagination = [
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'has_more_pages' => $paginator->hasMorePages(),
            'has_previous_pages' => $paginator->hasPreviousPages(),
        ];

        $allMeta = array_merge(['pagination' => $pagination], (array) $meta);

        return self::create(true, $message, $paginator->items(), 200, $allMeta);
    }

    /**
     * Collection response with metadata
     *
     * @param Collection $collection
     * @param string $message
     * @param mixed $meta
     * @return JsonResponse
     */
    public static function collection(Collection $collection, string $message = 'Data retrieved successfully', $meta = null): JsonResponse
    {
        $defaultMeta = [
            'count' => $collection->count(),
        ];

        $allMeta = array_merge($defaultMeta, (array) $meta);

        return self::create(true, $message, $collection->toArray(), 200, $allMeta);
    }

    /**
     * Download response
     *
     * @param string $path
     * @param string $name
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public static function download(string $path, string $name = null, array $headers = [])
    {
        return response()->download($path, $name, $headers);
    }

    /**
     * File response
     *
     * @param string $path
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public static function file(string $path, array $headers = [])
    {
        return response()->file($path, $headers);
    }

    /**
     * Stream response
     *
     * @param string $content
     * @param string $contentType
     * @param string $disposition
     * @return \Illuminate\Http\Response
     */
    public static function stream(string $content, string $contentType = 'text/plain', string $disposition = 'inline')
    {
        return response($content, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => $disposition,
        ]);
    }
}