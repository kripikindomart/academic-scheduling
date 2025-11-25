<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the users.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $filters = $request->only(['search', 'role', 'email_verified']);

        $users = $this->userService->getAllUsers($perPage, $filters);

        return ResponseService::paginated($users, 'Users retrieved successfully');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();

        // Validate data
        $validationErrors = $this->userService->validateUserData($data);
        if ($validationErrors) {
            return ResponseService::validationError($validationErrors);
        }

        // Create user
        $result = $this->userService->createUser($data);

        if ($result['success']) {
            return ResponseService::created($result['user'], $result['message']);
        } else {
            return ResponseService::internalServerError($result['message']);
        }
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return ResponseService::notFound('User not found');
        }

        return ResponseService::success($user, 'User retrieved successfully');
    }

    /**
     * Update the specified user in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;

        // Validate data
        $validationErrors = $this->userService->validateUserData($data, true);
        if ($validationErrors) {
            return ResponseService::validationError($validationErrors);
        }

        // Update user
        $result = $this->userService->updateUser($id, $data);

        if ($result['success']) {
            return ResponseService::success($result['user'], $result['message']);
        } else {
            return ResponseService::internalServerError($result['message']);
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $result = $this->userService->deleteUser($id);

        if ($result['success']) {
            return ResponseService::success(null, $result['message']);
        } else {
            return ResponseService::internalServerError($result['message']);
        }
    }

    /**
     * Get all available roles
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function roles()
    {
        $roles = $this->userService->getAllRoles();

        return ResponseService::success($roles, 'Roles retrieved successfully');
    }

    /**
     * Get all available permissions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function permissions()
    {
        $permissions = $this->userService->getAllPermissions();

        return ResponseService::success($permissions, 'Permissions retrieved successfully');
    }

    /**
     * Get user statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        $stats = $this->userService->getUserStatistics();

        return ResponseService::success($stats, 'User statistics retrieved successfully');
    }

    /**
     * Search users
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $request->validate([
            'term' => 'required|string|min:2',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        $term = $request->get('term');
        $perPage = $request->get('per_page', 20);

        $users = $this->userService->searchUsers($term, $perPage);

        return ResponseService::paginated($users, 'Search results retrieved successfully');
    }

    /**
     * Get users by role
     *
     * @param string $role
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function byRole($role, Request $request)
    {
        $perPage = $request->get('per_page', 20);

        $users = $this->userService->getUsersByRole($role, $perPage);

        return ResponseService::paginated($users, "Users with role '{$role}' retrieved successfully");
    }
}