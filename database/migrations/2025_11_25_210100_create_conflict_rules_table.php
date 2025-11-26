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
        Schema::create('conflict_rules', function (Blueprint $table) {
            $table->id();

            // Rule identification
            $table->string('rule_code', 50)->unique();
            $table->string('rule_name', 255);
            $table->text('rule_description');

            // Rule categorization
            $table->enum('rule_category', [
                'room_scheduling',
                'lecturer_availability',
                'class_scheduling',
                'time_slot_management',
                'capacity_constraints',
                'facility_requirements',
                'academic_constraints',
                'business_rules',
                'compliance_rules'
            ]);

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

            // Rule properties
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_blocking')->default(true); // Whether conflict blocks scheduling
            $table->boolean('auto_resolvable')->default(false);
            $table->integer('priority_score')->default(100); // Higher = more important

            // Rule conditions
            $table->json('conditions')->nullable(); // JSON conditions to check
            $table->json('parameters')->nullable(); // Rule parameters and thresholds
            $table->text('validation_logic')->nullable(); // Custom validation logic

            // Rule scope and applicability
            $table->json('applicable_to')->nullable(); // Which entities this applies to
            $table->json('exceptions')->nullable(); // Specific cases where rule doesn't apply
            $table->json('schedule_filters')->nullable(); // Filter criteria for schedules

            // Time-based rules
            $table->boolean('is_time_sensitive')->default(false);
            $table->json('time_constraints')->nullable(); // Specific time constraints
            $table->json('recurrence_patterns')->nullable();

            // Detection settings
            $table->string('detection_method', 100)->default('automated');
            $table->json('detection_algorithm')->nullable();
            $table->integer('detection_threshold')->default(0);

            // Resolution strategies
            $table->json('resolution_strategies')->nullable(); // Allowed resolution methods
            $table->json('resolution_priorities')->nullable(); // Preferred resolution order
            $table->json('resolution_constraints')->nullable(); // Constraints on resolution

            // Auto-resolution settings
            $table->json('auto_resolution_rules')->nullable();
            $table->json('resolution_templates')->nullable();
            $table->boolean('requires_approval')->default(false);

            // Notification settings
            $table->json('notification_rules')->nullable();
            $table->json('escalation_rules')->nullable();
            $table->json('notification_templates')->nullable();

            // Prevention measures
            $table->json('prevention_rules')->nullable();
            $table->json('prevention_suggestions')->nullable();
            $table->boolean('enforce_prevention')->default(false);

            // Compliance and governance
            $table->string('compliance_category')->nullable();
            $table->json('compliance_rules')->nullable();
            $table->boolean('is_mandatory')->default(false);

            // Rule metrics
            $table->integer('times_triggered')->default(0);
            $table->integer('times_resolved')->default(0);
            $table->decimal('success_rate', 5, 2)->default(0.00);
            $table->timestamp('last_triggered_at')->nullable();

            // Rule relationships
            $table->json('depends_on')->nullable(); // Other rules this depends on
            $table->json('conflicts_with')->nullable(); // Rules that conflict with this
            $table->json('overrides')->nullable(); // Rules this overrides

            // Rule versioning
            $table->string('version', 20)->default('1.0');
            $table->text('change_log')->nullable();
            $table->timestamp('effective_from')->nullable();
            $table->timestamp('effective_to')->nullable();

            // Audit and metadata
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['rule_category', 'conflict_type']);
            $table->index(['is_active', 'is_blocking']);
            $table->index(['severity', 'priority_score']);
            $table->index(['auto_resolvable', 'requires_approval']);
            $table->index(['detection_method']);
            $table->index(['times_triggered']);
            $table->index(['last_triggered_at']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conflict_rules');
    }
};