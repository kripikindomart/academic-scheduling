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
        // Drop tables with foreign key dependencies first
        Schema::dropIfExists('conflict_detections'); // Has FK to school_classes
        Schema::dropIfExists('conflict_rules'); // Has FK to users

        // Drop unused empty tables
        Schema::dropIfExists('school_classes'); // Duplicate of classes table (empty)
        Schema::dropIfExists('course_lecturers'); // Unused empty table
        Schema::dropIfExists('program_lecturers'); // Unused empty table
        Schema::dropIfExists('student_schedules'); // Unused empty table
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the tables if we need to rollback
        Schema::create('conflict_detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule1_id')->constrained('schedules')->onDelete('cascade');
            $table->foreignId('schedule2_id')->constrained('schedules')->onDelete('cascade');
            $table->enum('conflict_type', ['lecturer', 'room', 'student', 'time'])->default('lecturer');
            $table->text('description')->nullable();
            $table->enum('severity', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('resolved')->default(false);
            $table->timestamps();
        });

        Schema::create('conflict_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('conditions');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('course_lecturers', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['main_lecturer', 'assistant_lecturer', 'coordinator'])->default('main_lecturer');
            $table->timestamp('assigned_at')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->primary(['course_id', 'user_id']);
            $table->index(['course_id', 'role']);
            $table->index(['user_id', 'role']);
        });

        Schema::create('program_lecturers', function (Blueprint $table) {
            $table->foreignId('program_study_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['coordinator', 'member'])->default('member');
            $table->timestamp('assigned_at')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->primary(['program_study_id', 'user_id']);
            $table->index(['program_study_id', 'role']);
            $table->index(['user_id', 'role']);
        });

        Schema::create('student_schedules', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['enrolled', 'dropped', 'completed'])->default('enrolled');
            $table->timestamp('enrollment_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->primary(['student_id', 'schedule_id']);
            $table->index(['student_id', 'status']);
            $table->index(['schedule_id', 'status']);
        });

        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('class_name', 100);
            $table->string('class_code', 50)->unique();
            $table->foreignId('program_study_id')->constrained()->onDelete('cascade');
            $table->string('academic_year', 20);
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
            $table->integer('year_level');
            $table->integer('capacity')->default(40);
            $table->integer('current_students')->default(0);
            $table->foreignId('academic_advisor_id')->nullable()->constrained('lecturers')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['program_study_id', 'academic_year', 'semester']);
            $table->index(['class_code']);
            $table->index(['is_active']);
            $table->index('deleted_at');
        });
    }
};
