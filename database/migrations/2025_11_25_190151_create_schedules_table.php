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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            // Schedule identification
            $table->string('schedule_code', 50)->unique();
            $table->string('title');
            $table->text('description')->nullable();

            // Schedule details
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('day_of_week', 20); // monday, tuesday, etc.
            $table->integer('duration_minutes'); // total duration in minutes

            // Schedule type and recurrence
            $table->enum('schedule_type', ['single', 'recurring', 'exam', 'extra'])->default('single');
            $table->boolean('is_recurring')->default(false);
            $table->json('recurrence_pattern')->nullable(); // JSON for complex recurrence rules
            $table->date('recurrence_end_date')->nullable();

            // Related entities
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lecturer_id')->nullable()->constrained('lecturers')->onDelete('set null');
            $table->foreignId('room_id')->constrained()->onDelete('restrict');
            $table->foreignId('program_study_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->onDelete('set null');

            // Academic period
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
            $table->string('academic_year', 20); // e.g., "2023/2024"
            $table->integer('week_number')->nullable(); // week in semester

            // Schedule status and workflow
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'cancelled', 'completed'])->default('draft');
            $table->enum('conflict_status', ['none', 'detected', 'resolved'])->default('none');
            $table->text('conflict_details')->nullable();
            $table->text('rejection_reason')->nullable();

            // Attendance and capacity
            $table->integer('expected_attendees')->default(0);
            $table->integer('actual_attendees')->default(0);
            $table->decimal('attendance_rate', 5, 2)->default(0);

            // Additional properties
            $table->enum('session_type', ['lecture', 'lab', 'seminar', 'tutorial', 'exam', 'meeting'])->default('lecture');
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_online')->default(false);
            $table->string('meeting_link')->nullable();
            $table->text('notes')->nullable();
            $table->text('materials')->nullable(); // JSON array of materials

            // Schedule metadata
            $table->boolean('is_published')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('locked_at')->nullable();

            // Approval workflow
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();

            // Cancellation details
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Rescheduling
            $table->foreignId('rescheduled_from')->nullable()->constrained('schedules')->onDelete('set null');
            $table->text('reschedule_reason')->nullable();

            // Audit and tracking
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('last_modified_at')->nullable();
            $table->ipAddress('created_from_ip')->nullable();
            $table->string('user_agent', 500)->nullable();

            // Soft deletes
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            // Indexes for performance
            $table->index(['date', 'start_time', 'end_time']);
            $table->index(['course_id', 'semester', 'academic_year']);
            $table->index(['lecturer_id', 'date']);
            $table->index(['room_id', 'date']);
            $table->index(['status']);
            $table->index(['is_published']);
            $table->index(['program_study_id']);
            $table->index(['schedule_type']);
            $table->index(['day_of_week']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
