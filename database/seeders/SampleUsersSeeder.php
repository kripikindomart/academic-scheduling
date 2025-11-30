<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ProgramStudy;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SampleUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all program studies
        $programStudies = ProgramStudy::select('id', 'name', 'code')->get();

        if ($programStudies->isEmpty()) {
            $this->command->error('❌ No program studies found. Please run program studies seeder first.');
            return;
        }

        $this->command->info('🎓 Creating sample users with program study assignments...');

        // Create users for each program study
        $sampleUsers = [
            [
                'name' => 'Ahmad Wijaya',
                'email' => 'ahmad.wijaya@example.com',
                'role' => 'lecturer',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@example.com',
                'role' => 'lecturer',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'role' => 'staff',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@example.com',
                'role' => 'lecturer',
            ],
            [
                'name' => 'Rudi Hermawan',
                'email' => 'rudi.hermawan@example.com',
                'role' => 'staff',
            ],
            [
                'name' => 'Maria Ulfa',
                'email' => 'maria.ulfa@example.com',
                'role' => 'lecturer',
            ],
            [
                'name' => 'Joko Prasetyo',
                'email' => 'joko.prasetyo@example.com',
                'role' => 'staff',
            ]
        ];

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($sampleUsers as $index => $userData) {
            // Assign program study cyclically
            $programStudy = $programStudies[$index % $programStudies->count()];

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password123'),
                    'role' => $userData['role'],
                    'program_study_id' => $programStudy->id,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            if ($user->wasRecentlyCreated) {
                $createdCount++;
                $this->command->line("   ✅ Created: {$userData['name']} ({$userData['role']}) - {$programStudy->name}");
            } else {
                $updatedCount++;
                $this->command->line("   🔄 Updated: {$userData['name']} ({$userData['role']}) - {$programStudy->name}");
            }
        }

        // Create a few students for testing
        $this->command->info("\n📚 Creating sample students...");

        $sampleStudents = [
            ['name' => 'Andi Rahman', 'email' => 'andi.rahman@student.example.com'],
            ['name' => 'Maya Putri', 'email' => 'maya.putri@student.example.com'],
            ['name' => 'Rizky Aditya', 'email' => 'rizky.aditya@student.example.com'],
            ['name' => 'Saskia Aulia', 'email' => 'saskia.aulia@student.example.com'],
            ['name' => 'Fajar Budi', 'email' => 'fajar.budi@student.example.com'],
        ];

        foreach ($sampleStudents as $index => $studentData) {
            // Assign to first 3 program studies for testing
            $programStudy = $programStudies[$index % 3];

            $user = User::updateOrCreate(
                ['email' => $studentData['email']],
                [
                    'name' => $studentData['name'],
                    'email' => $studentData['email'],
                    'password' => Hash::make('password123'),
                    'role' => 'student',
                    'program_study_id' => $programStudy->id,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            if ($user->wasRecentlyCreated) {
                $createdCount++;
                $this->command->line("   ✅ Created Student: {$studentData['name']} - {$programStudy->name}");
            }
        }

        // Display summary
        $this->command->info("\n📋 Summary:");
        $this->command->info("   Total Created: {$createdCount} users");
        $this->command->info("   Total Updated: {$updatedCount} users");
        $this->command->info("   Total Program Studies: {$programStudies->count()}");

        $this->command->info("\n🔐 Test Credentials:");
        $this->command->info("   Email: ahmad.wijaya@example.com");
        $this->command->info("   Password: password123");
        $this->command->info("   Role: lecturer");

        $this->command->info("\n   Email: andi.rahman@student.example.com");
        $this->command->info("   Password: password123");
        $this->command->info("   Role: student");

        $this->command->info("\n✅ Sample users created successfully!");
    }
}