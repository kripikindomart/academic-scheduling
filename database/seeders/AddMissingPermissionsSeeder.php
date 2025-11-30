<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AddMissingPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create all required permissions
        $permissions = [
            // Students permissions
            'students.view',
            'students.create',
            'students.edit',
            'students.delete',

            // Lecturers permissions
            'lecturers.view',
            'lecturers.create',
            'lecturers.edit',
            'lecturers.delete',

            // Program Studies permissions
            'program_studies.view',
            'program_studies.create',
            'program_studies.edit',
            'program_studies.delete',

            // Courses permissions
            'courses.view',
            'courses.create',
            'courses.edit',
            'courses.delete',

            // Rooms permissions
            'rooms.view',
            'rooms.create',
            'rooms.edit',
            'rooms.delete',

            // Classes permissions
            'classes.view',
            'classes.create',
            'classes.edit',
            'classes.delete',
            'classes.manage',

            // Schedules permissions
            'schedules.view',
            'schedules.create',
            'schedules.edit',
            'schedules.delete',
            'schedules.manage',
            'schedules.own',

            // Users permissions
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Other permissions
            'reports.view',
            'reports.create',
            'reports.edit',
            'reports.delete',
            'reports.academic',
            'attendance.view',
            'attendance.create',
            'attendance.edit',
            'attendance.delete',
            'attendance.own',
            'grades.view',
            'grades.create',
            'grades.edit',
            'grades.delete',
            'grades.own',
            'journals.view',
            'journals.create',
            'journals.edit',
            'journals.delete',
            'journals.own',
            'sk.approve',
            'basic',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'sanctum'
            ]);
        }

        // Get Super Admin role and give all permissions
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }

        // Update Admin role with more permissions
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminPermissions = [
                'students.view', 'students.create', 'students.edit', 'students.delete',
                'lecturers.view', 'lecturers.create', 'lecturers.edit', 'lecturers.delete',
                'program_studies.view', 'program_studies.create', 'program_studies.edit', 'program_studies.delete',
                'courses.view', 'courses.create', 'courses.edit', 'courses.delete',
                'rooms.view', 'rooms.create', 'rooms.edit', 'rooms.delete',
                'classes.view', 'classes.create', 'classes.edit', 'classes.delete', 'classes.manage',
                'schedules.view', 'schedules.create', 'schedules.edit', 'schedules.delete',
                'users.view', 'users.create', 'users.edit', 'users.delete',
                'reports.view', 'reports.create', 'reports.edit', 'reports.delete',
                'attendance.view', 'attendance.create', 'attendance.edit', 'attendance.delete',
                'grades.view', 'grades.create', 'grades.edit', 'grades.delete',
                'journals.view', 'journals.create', 'journals.edit', 'journals.delete',
            ];
            $adminRole->givePermissionTo($adminPermissions);
        }

        // Update Kaprodi role
        $kaprodiRole = Role::where('name', 'Kaprodi')->first();
        if ($kaprodiRole) {
            $kaprodiPermissions = [
                'students.view', 'students.create', 'students.edit',
                'lecturers.view', 'lecturers.create',
                'program_studies.view', 'program_studies.edit',
                'courses.view', 'courses.edit',
                'classes.view', 'classes.manage',
                'schedules.view', 'schedules.manage',
                'reports.view', 'reports.academic',
                'grades.view', 'grades.edit',
                'attendance.view',
                'sk.approve',
            ];
            $kaprodiRole->givePermissionTo($kaprodiPermissions);
        }

        // Update Staff role
        $staffRole = Role::where('name', 'Staff')->first();
        if ($staffRole) {
            $staffPermissions = [
                'students.view', 'students.create', 'students.edit',
                'lecturers.view',
                'program_studies.view',
                'courses.view',
                'rooms.view', 'rooms.create', 'rooms.edit',
                'classes.view',
                'schedules.view',
                'attendance.view', 'attendance.create', 'attendance.edit',
                'grades.view',
                'basic',
            ];
            $staffRole->givePermissionTo($staffPermissions);
        }

        $this->command->info('Missing permissions added and roles updated successfully.');
    }
}