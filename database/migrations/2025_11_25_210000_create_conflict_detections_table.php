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
        Schema::create('conflict_detections', function (Blueprint $table) {
            $table->id();

            // Conflict identification
            $table->string('conflict_id', 50)->unique();
            $table->enum('conflict_type', [
                'room_conflict',
                'lecturer_conflict',
                'class_conflict',
                'time_slot_conflict',
                'capacity_conflict',
                'facility_conflict',
                'prerequisite_conflict',
                'academic_load_conflict'
            ]);

            // Primary schedule that caused the conflict
            $table->foreignId('primary_schedule_id')->constrained('schedules')->onDelete('cascade');

            // Conflicting schedule(s)
            $table->foreignId('conflicting_schedule_id')->nullable()->constrained('schedules')->onDelete('cascade');
            $table->json('conflicting_schedules')->nullable(); // For multiple conflicts

            // Conflict details
            $table->string('conflict_title', 255);
            $table->text('conflict_description');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['detected', 'reviewing', 'resolved', 'ignored'])->default('detected');

            // Conflict context
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('lecturer_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('program_study_id')->nullable()->constrained()->onDelete('cascade');

            // Time conflict details
            $table->date('conflict_date');
            $table->time('conflict_start_time');
            $table->time('conflict_end_time');
            $table->integer('conflict_duration_minutes')->default(0);

            // Detection metadata
            $table->string('detection_method', 100)->default('automated'); // automated, manual, import
            $table->json('detection_rules')->nullable(); // Rules that triggered this conflict
            $table->json('conflict_data')->nullable(); // Additional conflict context data

            // Resolution tracking
            $table->enum('resolution_strategy', [
                'none',
                'reschedule_primary',
                'reschedule_conflicting',
                'change_room',
                'change_lecturer',
                'change_class',
                'adjust_time',
                'override',
                'manual_resolution'
            ])->default('none');

            $table->text('resolution_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();

            // Resolution details
            $table->json('original_schedule_data')->nullable(); // Backup of original data
            $table->json('resolution_data')->nullable(); // Changes made for resolution
            $table->boolean('is_resolution_permanent')->default(false);

            // Impact assessment
            $table->integer('affected_students_count')->default(0);
            $table->decimal('impact_score', 5, 2)->default(0.00); // Calculated impact severity
            $table->json('affected_stakeholders')->nullable(); // students, lecturers, etc

            // Escalation and approval
            $table->boolean('requires_approval')->default(false);
            $table->foreignId('escalated_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('escalated_at')->nullable();
            $table->text('escalation_reason')->nullable();

            // Notifications
            $table->json('notification_recipients')->nullable();
            $table->json('notification_history')->nullable();
            $table->boolean('notifications_sent')->default(false);

            // Auto-resolution settings
            $table->boolean('auto_resolvable')->default(false);
            $table->json('auto_resolution_rules')->nullable();
            $table->integer('resolution_priority')->default(1); // Higher number = higher priority

            // Prevention measures
            $table->json('prevention_rules')->nullable(); // Rules to prevent similar conflicts
            $table->boolean('recurring_conflict')->default(false);
            $table->string('recurrence_pattern', 100)->nullable();

            // Audit and compliance
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['conflict_type', 'status']);
            $table->index(['severity', 'status']);
            $table->index(['conflict_date']);
            $table->index(['room_id', 'conflict_date']);
            $table->index(['lecturer_id', 'conflict_date']);
            $table->index(['class_id', 'conflict_date']);
            $table->index(['primary_schedule_id']);
            $table->index(['conflicting_schedule_id']);
            $table->index(['detection_method']);
            $table->index(['resolution_strategy']);
            $table->index(['requires_approval']);
            $table->index(['auto_resolvable']);
            $table->index(['recurring_conflict']);
            $table->index(['created_at']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conflict_detections');
    }
};