<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class WhatsAppPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // WhatsApp Gateway permissions
        $permissions = [
            // View permissions
            'whatsapp.view' => 'View WhatsApp Gateway',

            // Manage permissions
            'whatsapp.manage' => 'Manage WhatsApp sessions and connections',

            // Send permissions
            'whatsapp.send' => 'Send WhatsApp messages',

            // Notification permissions
            'whatsapp.notifications' => 'Send WhatsApp notifications',
        ];

        foreach ($permissions as $name => $description) {
            // Create for both web and sanctum guards
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web'
            ]);

            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'sanctum'
            ]);
        }

        // Assign permissions to super admin role
        $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo(array_keys($permissions));
        }

        // Assign permissions to admin role
        $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(array_keys($permissions));
        }

        $this->command->info('WhatsApp permissions created successfully.');
    }
}
