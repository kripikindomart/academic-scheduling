<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user for created_by and updated_by fields
        $adminUser = User::where('email', 'admin@pasca.ac.id')->first();
        if (!$adminUser) {
            $adminUser = User::whereHas('roles', function($query) {
                $query->where('name', 'Super Admin');
            })->first();
        }

        $createdById = $adminUser ? $adminUser->id : 1;
        $now = Carbon::now();

        // Academic Year 2023/2024 - Completed
        DB::table('academic_years')->insert([
            'name' => 'Tahun Akademik 2023/2024',
            'code' => '2023/2024',
            'description' => 'Tahun akademik 2023/2024 dengan dua semester regular',
            'start_date' => '2023-09-01',
            'end_date' => '2024-08-31',
            'is_active' => false,
            'status' => 'completed',
            'current_semester' => 2,
            'registration_start_date' => '2023-06-01',
            'registration_end_date' => '2023-07-31',
            'course_registration_start_date' => '2023-08-01',
            'course_registration_end_date' => '2023-08-25',
            'semester_1_start_date' => '2023-09-01',
            'semester_1_end_date' => '2024-01-31',
            'semester_1_break_start' => '2023-12-23',
            'semester_1_break_end' => '2024-01-07',
            'semester_2_start_date' => '2024-02-01',
            'semester_2_end_date' => '2024-06-30',
            'semester_2_break_start' => '2024-03-25',
            'semester_2_break_end' => '2024-03-31',
            'final_exam_start_date' => '2024-06-10',
            'final_exam_end_date' => '2024-06-25',
            'grading_period_start' => '2024-06-26',
            'grading_period_end' => '2024-07-15',
            'max_credit_semester_1' => 24,
            'max_credit_semester_2' => 24,
            'tuition_fee' => 12000000.00,
            'registration_fee' => 2500000.00,
            'is_visible_to_students' => true,
            'allow_course_registration' => false,
            'allow_schedule_changes' => false,
            'settings' => json_encode([
                'late_registration_fee' => 500000,
                'max_courses_per_semester' => 8,
                'min_courses_per_semester' => 3,
                'grading_system' => 'A-F',
                'attendance_requirement' => 75,
                'academic_calendar_url' => 'https://pasca.ac.id/kalender-akademik/2023-2024'
            ]),
            'created_by' => $createdById,
            'updated_by' => $createdById,
            'activated_by' => $createdById,
            'activated_at' => '2023-08-01 10:00:00',
            'created_at' => $now->copy()->subMonths(20),
            'updated_at' => $now->copy()->subMonths(8),
        ]);

        // Academic Year 2024/2025 - Active
        DB::table('academic_years')->insert([
            'name' => 'Tahun Akademik 2024/2025',
            'code' => '2024/2025',
            'description' => 'Tahun akademik 2024/2025 dengan dua semester regular dan sistem pembelajaran hybrid',
            'start_date' => '2024-09-01',
            'end_date' => '2025-08-31',
            'is_active' => true,
            'status' => 'active',
            'current_semester' => 1,
            'registration_start_date' => '2024-06-01',
            'registration_end_date' => '2024-07-31',
            'course_registration_start_date' => '2024-08-01',
            'course_registration_end_date' => '2024-08-28',
            'semester_1_start_date' => '2024-09-02',
            'semester_1_end_date' => '2025-01-31',
            'semester_1_break_start' => '2024-12-21',
            'semester_1_break_end' => '2025-01-05',
            'semester_2_start_date' => '2025-02-01',
            'semester_2_end_date' => '2025-06-30',
            'semester_2_break_start' => '2025-03-24',
            'semester_2_break_end' => '2025-03-30',
            'final_exam_start_date' => '2025-06-09',
            'final_exam_end_date' => '2025-06-24',
            'grading_period_start' => '2025-06-25',
            'grading_period_end' => '2025-07-20',
            'max_credit_semester_1' => 24,
            'max_credit_semester_2' => 24,
            'tuition_fee' => 15000000.00,
            'registration_fee' => 3000000.00,
            'is_visible_to_students' => true,
            'allow_course_registration' => true,
            'allow_schedule_changes' => true,
            'settings' => json_encode([
                'late_registration_fee' => 750000,
                'max_courses_per_semester' => 8,
                'min_courses_per_semester' => 3,
                'grading_system' => 'A-F',
                'attendance_requirement' => 80,
                'academic_calendar_url' => 'https://pasca.ac.id/kalender-akademik/2024-2025',
                'hybrid_learning' => true,
                'online_platform' => 'Google Classroom',
                'library_access' => true,
                'student_services' => ['academic_advising', 'counseling', 'career_services']
            ]),
            'created_by' => $createdById,
            'updated_by' => $createdById,
            'activated_by' => $createdById,
            'activated_at' => '2024-08-01 09:00:00',
            'created_at' => $now->copy()->subMonths(8),
            'updated_at' => $now->copy()->subDays(15),
        ]);

        // Academic Year 2025/2026 - Upcoming
        DB::table('academic_years')->insert([
            'name' => 'Tahun Akademik 2025/2026',
            'code' => '2025/2026',
            'description' => 'Tahun akademik 2025/2026 dengan kurikulum baru dan program studi lanjutan',
            'start_date' => '2025-09-01',
            'end_date' => '2026-08-31',
            'is_active' => false,
            'status' => 'upcoming',
            'current_semester' => 1,
            'registration_start_date' => '2025-05-01',
            'registration_end_date' => '2025-07-31',
            'course_registration_start_date' => '2025-08-01',
            'course_registration_end_date' => '2025-08-30',
            'semester_1_start_date' => '2025-09-01',
            'semester_1_end_date' => '2026-01-31',
            'semester_1_break_start' => '2025-12-20',
            'semester_1_break_end' => '2026-01-04',
            'semester_2_start_date' => '2026-02-01',
            'semester_2_end_date' => '2026-06-30',
            'semester_2_break_start' => '2026-03-23',
            'semester_2_break_end' => '2026-03-29',
            'final_exam_start_date' => '2026-06-08',
            'final_exam_end_date' => '2026-06-23',
            'grading_period_start' => '2026-06-24',
            'grading_period_end' => '2026-07-15',
            'max_credit_semester_1' => 24,
            'max_credit_semester_2' => 24,
            'tuition_fee' => 17500000.00,
            'registration_fee' => 3500000.00,
            'is_visible_to_students' => true,
            'allow_course_registration' => false,
            'allow_schedule_changes' => false,
            'settings' => json_encode([
                'late_registration_fee' => 1000000,
                'max_courses_per_semester' => 9,
                'min_courses_per_semester' => 3,
                'grading_system' => 'A-F',
                'attendance_requirement' => 85,
                'academic_calendar_url' => 'https://pasca.ac.id/kalender-akademik/2025-2026',
                'new_curriculum' => true,
                'international_collaboration' => true,
                'research_opportunities' => true,
                'student_services' => ['academic_advising', 'counseling', 'career_services', 'international_office']
            ]),
            'created_by' => $createdById,
            'updated_by' => $createdById,
            'activated_at' => null,
            'created_at' => $now->copy()->subMonths(2),
            'updated_at' => $now->copy()->subMonths(2),
        ]);

        $this->command->info('✅ Academic years seeded successfully!');
        $this->command->info('   - 2023/2024 (Completed)');
        $this->command->info('   - 2024/2025 (Active)');
        $this->command->info('   - 2025/2026 (Upcoming)');
    }
}