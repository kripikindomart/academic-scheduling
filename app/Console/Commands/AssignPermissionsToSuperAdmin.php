<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignPermissionsToSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-permissions-to-super-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign all necessary permissions to super admin role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Assigning permissions to Super Admin role...');

        // Get or create super admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        $this->info('✅ Super Admin role found/created');

        // List of all permissions needed for the system
        $permissions = [
            // User management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Program studies management
            'program_studies.view',
            'program_studies.create',
            'program_studies.edit',
            'program_studies.delete',

            // Courses management
            'courses.view',
            'courses.create',
            'courses.edit',
            'courses.delete',

            // Students management
            'students.view',
            'students.create',
            'students.edit',
            'students.delete',

            // Lecturers management
            'lecturers.view',
            'lecturers.create',
            'lecturers.edit',
            'lecturers.delete',

            // Rooms management
            'rooms.view',
            'rooms.create',
            'rooms.edit',
            'rooms.delete',

            // Schedule management
            'schedules.view',
            'schedules.create',
            'schedules.edit',
            'schedules.delete',

            // Dashboard access
            'dashboard.view',
        ];

        $createdPermissions = 0;
        $assignedPermissions = 0;

        foreach ($permissions as $permissionName) {
            // Create permission if it doesn't exist
            $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);

            // Check if permission already assigned
            if (!$superAdminRole->hasPermissionTo($permission)) {
                $superAdminRole->givePermissionTo($permission);
                $assignedPermissions++;
            }

            $createdPermissions++;
        }

        // Find users with super admin role and ensure they have the role
        $superAdminUsers = User::whereHas('roles', function($query) {
            $query->where('name', 'super admin');
        })->get();

        foreach ($superAdminUsers as $user) {
            if (!$user->hasRole('super admin')) {
                $user->assignRole('super admin');
                $this->info("✅ Assigned super admin role to user: {$user->name} ({$user->email})");
            }
        }

        $this->info("✅ Successfully processed {$createdPermissions} permissions");
        $this->info("✅ Successfully assigned {$assignedPermissions} new permissions to Super Admin role");
        $this->info("✅ Found {$superAdminUsers->count()} users with super admin role");

        $this->info('🎉 Permission assignment completed!');

        return Command::SUCCESS;
    }
}
