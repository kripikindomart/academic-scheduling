<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class AcademicYearsPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for academic years
        $permissions = [
            [
                'name' => 'academic_years.view',
                'group_name' => 'Tahun Akademik',
                'description' => 'View academic years data',
            ],
            [
                'name' => 'academic_years.create',
                'group_name' => 'Tahun Akademik',
                'description' => 'Create new academic year',
            ],
            [
                'name' => 'academic_years.edit',
                'group_name' => 'Tahun Akademik',
                'description' => 'Edit academic year data',
            ],
            [
                'name' => 'academic_years.delete',
                'group_name' => 'Tahun Akademik',
                'description' => 'Delete academic year',
            ],
            [
                'name' => 'academic_years.manage',
                'group_name' => 'Tahun Akademik',
                'description' => 'Manage academic year settings (set active year)',
            ],
        ];

        // Create permission names array
        $permissionNames = [
            'academic_years.view',
            'academic_years.create',
            'academic_years.edit',
            'academic_years.delete',
            'academic_years.manage'
        ];

        // Create permissions with sanctum guard
        foreach ($permissionNames as $permissionName) {
            Permission::updateOrCreate(
                ['name' => $permissionName],
                [
                    'guard_name' => 'sanctum',
                ]
            );
        }

        // Assign permissions to Super Admin role (already has * permissions)
        // Super Admin already has all permissions via ['*']

        // Assign permissions to Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminPermissions = [];
            foreach ($permissionNames as $permissionName) {
                $adminPermissions[] = $permissionName;
            }
            $adminRole->givePermissionTo($adminPermissions);
        }

        $this->command->info('Academic Years permissions created successfully!');
    }
}
