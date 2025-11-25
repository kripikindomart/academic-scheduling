<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DefaultRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create roles
        $roles = [
            [
                'name' => 'Super Admin',
                'guard_name' => 'sanctum',
                'permissions' => ['*'],
            ],
            [
                'name' => 'Admin',
                'guard_name' => 'sanctum',
                'permissions' => ['schedules.*', 'users.view', 'reports.*'],
            ],
            [
                'name' => 'Kaprodi',
                'guard_name' => 'sanctum',
                'permissions' => ['schedules.manage', 'sk.approve', 'reports.academic'],
            ],
            [
                'name' => 'Dosen',
                'guard_name' => 'sanctum',
                'permissions' => ['schedules.own', 'journals.own', 'grades.own'],
            ],
            [
                'name' => 'Staff',
                'guard_name' => 'sanctum',
                'permissions' => ['schedules.view', 'attendance.*', 'basic'],
            ],
            [
                'name' => 'Student',
                'guard_name' => 'sanctum',
                'permissions' => ['schedules.view', 'attendance.own', 'grades.own'],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate([
                'name' => $roleData['name'],
                'guard_name' => $roleData['guard_name']
            ], [
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create permissions if they don't exist
            if ($roleData['permissions'] === ['*']) {
                // Super admin gets all permissions
                continue;
            }

            foreach ($roleData['permissions'] as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web'
                ]);
            }
        }

        $this->command->info('Default roles and permissions created successfully.');
    }
}