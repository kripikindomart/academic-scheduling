<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class UserService extends BaseService
{
    /**
     * Get all users with pagination
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllUsers(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = User::with('roles', 'permissions')
            ->select('id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at');

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        if (!empty($filters['email_verified'])) {
            $query->whereNotNull('email_verified_at');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get user by ID
     *
     * @param int $id
     * @return User|null
     */
    public function getUserById(int $id): ?User
    {
        return User::with('roles', 'permissions')->find($id);
    }

    /**
     * Create new user
     *
     * @param array $data
     * @return array
     */
    public function createUser(array $data): array
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
            ]);

            // Assign roles
            $roles = $data['roles'] ?? ['Student'];
            $user->assignRole($roles);

            // Load relationships
            $user->load('roles', 'permissions');

            DB::commit();

            $this->logInfo('User created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'roles' => $roles
            ]);

            return [
                'success' => true,
                'user' => $user,
                'message' => 'User created successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            $this->logError('Failed to create user', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateUser(int $id, array $data): array
    {
        try {
            $user = User::findOrFail($id);

            DB::beginTransaction();

            $updateData = $data;

            // Handle password update
            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            } else {
                unset($updateData['password']);
            }

            $user->update($updateData);

            // Update roles if provided
            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            // Load relationships
            $user->load('roles', 'permissions');

            DB::commit();

            $this->logInfo('User updated successfully', [
                'user_id' => $user->id,
                'updated_fields' => array_keys($data)
            ]);

            return [
                'success' => true,
                'user' => $user,
                'message' => 'User updated successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            $this->logError('Failed to update user', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete user
     *
     * @param int $id
     * @return array
     */
    public function deleteUser(int $id): array
    {
        try {
            $user = User::findOrFail($id);

            DB::beginTransaction();

            // Delete all tokens
            $user->tokens()->delete();

            // Delete the user
            $user->delete();

            DB::commit();

            $this->logInfo('User deleted successfully', [
                'user_id' => $id,
                'email' => $user->email
            ]);

            return [
                'success' => true,
                'message' => 'User deleted successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            $this->logError('Failed to delete user', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all roles
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllRoles()
    {
        return Role::all(['id', 'name', 'guard_name']);
    }

    /**
     * Get all permissions
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPermissions()
    {
        return \Spatie\Permission\Models\Permission::all(['id', 'name', 'guard_name']);
    }

    /**
     * Get users by role
     *
     * @param string $roleName
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUsersByRole(string $roleName, int $perPage = 20): LengthAwarePaginator
    {
        return User::role($roleName)
            ->with('roles', 'permissions')
            ->select('id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Search users
     *
     * @param string $term
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchUsers(string $term, int $perPage = 20): LengthAwarePaginator
    {
        return User::with('roles', 'permissions')
            ->where('name', 'LIKE', "%{$term}%")
            ->orWhere('email', 'LIKE', "%{$term}%")
            ->select('id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get user statistics
     *
     * @return array
     */
    public function getUserStatistics(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::whereNotNull('email_verified_at')->count();
        $inactiveUsers = $totalUsers - $activeUsers;

        $usersByRole = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->groupBy('roles.name')
            ->selectRaw('roles.name as role, COUNT(*) as count')
            ->get()
            ->keyBy('role');

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'inactive_users' => $inactiveUsers,
            'users_by_role' => $usersByRole->toArray(),
        ];
    }

    /**
     * Validate user data
     *
     * @param array $data
     * @param bool $isUpdate
     * @return array|null
     */
    public function validateUserData(array $data, bool $isUpdate = false): ?array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => $isUpdate ? 'required|email|max:255|unique:users,email,' . ($data['id'] ?? '') : 'required|email|max:255|unique:users',
        ];

        if (!$isUpdate) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } elseif (!empty($data['password'])) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $rules['roles'] = 'array';
        $rules['roles.*'] = 'exists:roles,name';

        return $this->validate($rules, $data);
    }
}