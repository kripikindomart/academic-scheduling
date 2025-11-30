<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();

            // Academic Year Identification
            $table->string('name', 50); // e.g., "Tahun Akademik 2025/2026"
            $table->string('code', 20)->unique(); // e.g., "2025", "TA2025"
            $table->string('academic_calendar_year', 9); // e.g., "2025-2026"
            $table->text('description')->nullable(); // Optional description

            // Date Range
            $table->date('start_date'); // Start date of academic year
            $table->date('end_date'); // End date of academic year

            // Status and Configuration
            $table->boolean('is_active')->default(false); // Only one can be active
            $table->enum('status', ['upcoming', 'active', 'completed'])->default('upcoming');

            // S2 Indonesia System - Admission Period (Ganjil/Genap)
            $table->enum('admission_period', ['ganjil', 'genap'])->default('ganjil'); // Ganjil: Sept-Feb, Genap: Mar-Aug

            // Admission & Registration Periods
            $table->date('admission_start_date')->nullable(); // Start of student admission
            $table->date('admission_end_date')->nullable(); // End of student admission
            $table->date('registration_start_date')->nullable(); // Start of new student registration
            $table->date('registration_end_date')->nullable(); // End of new student registration
            $table->date('course_registration_start_date')->nullable(); // Start of course registration
            $table->date('course_registration_end_date')->nullable(); // End of course registration

            // Academic Calendar
            $table->date('class_start_date')->nullable(); // Start of classes
            $table->date('class_end_date')->nullable(); // End of classes
            $table->date('mid_exam_start_date')->nullable(); // Mid-term exam period start
            $table->date('mid_exam_end_date')->nullable(); // Mid-term exam period end
            $table->date('final_exam_start_date')->nullable(); // Final exam period start
            $table->date('final_exam_end_date')->nullable(); // Final exam period end

            // S2 Specific Deadlines
            $table->date('thesis_deadline')->nullable(); // Thesis submission deadline
            $table->date('yudisium_date')->nullable(); // Yudisium ceremony date

            // Settings and Limits
            $table->integer('max_credit_per_semester')->default(12); // Max credits per semester (S2 usually 12-15)
            $table->decimal('tuition_fee', 12, 2)->nullable(); // Base tuition fee per semester
            $table->decimal('registration_fee', 12, 2)->nullable(); // Admission fee

            // System Fields
            $table->boolean('is_visible_to_students')->default(true); // Students can see this year
            $table->boolean('allow_course_registration')->default(false); // Allow course registration
            $table->boolean('allow_schedule_changes')->default(true); // Allow schedule modifications
            $table->json('settings')->nullable(); // Additional settings as JSON

            // Audit Fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('activated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('activated_at')->nullable(); // When this year was activated
            $table->softDeletes();

            // Indexes
            $table->index(['name']);
            $table->index(['code']);
            $table->index(['is_active']);
            $table->index(['status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['admission_period']);
            $table->index(['academic_calendar_year']);
            $table->index('deleted_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};