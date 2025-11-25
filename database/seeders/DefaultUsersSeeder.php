<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Super Admin user
        $superAdmin = User::firstOrCreate([
            'email' => 'admin@jadwal-app.com',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdmin->assignRole($superAdminRole);
        }

        // Create sample users for testing
        $testUsers = [
            [
                'name' => 'Dosen Ahmad',
                'email' => 'dosen@jadwal-app.com',
                'password' => Hash::make('password'),
                'role' => 'Dosen',
            ],
            [
                'name' => 'Kaprodi Informatika',
                'email' => 'kaprodi@jadwal-app.com',
                'password' => Hash::make('password'),
                'role' => 'Kaprodi',
            ],
            [
                'name' => 'Staff Akademik',
                'email' => 'staff@jadwal-app.com',
                'password' => Hash::make('password'),
                'role' => 'Staff',
            ],
            [
                'name' => 'Mahasiswa Budi',
                'email' => 'mahasiswa@jadwal-app.com',
                'password' => Hash::make('password'),
                'role' => 'Student',
            ],
        ];

        foreach ($testUsers as $userData) {
            $user = User::firstOrCreate([
                'email' => $userData['email'],
            ], [
                'name' => $userData['name'],
                'password' => $userData['password'],
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                $user->assignRole($role);
            }
        }

        $this->command->info('Default users created successfully.');
        $this->command->info('Email: admin@jadwal-app.com, Password: password');
        $this->command->info('Other test users: dosen@jadwal-app.com, kaprodi@jadwal-app.com, staff@jadwal-app.com, mahasiswa@jadwal-app.com (all password: password)');
    }
}