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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_code', 20)->unique();
            $table->string('name', 255);
            $table->string('building', 100);
            $table->integer('floor');
            $table->enum('room_type', ['classroom', 'laboratory', 'seminar_room', 'auditorium', 'workshop', 'library', 'office', 'meeting_room', 'multipurpose']);
            $table->integer('capacity');
            $table->integer('current_occupancy')->default(0);
            $table->decimal('area', 8, 2)->nullable(); // Square meters
            $table->string('department', 255)->nullable();
            $table->string('faculty', 255)->nullable();
            $table->text('location')->nullable();
            $table->text('description')->nullable();
            $table->json('facilities')->nullable(); // ['projector', 'whiteboard', 'ac', 'computer', 'wifi', etc]
            $table->json('equipment')->nullable(); // Specific equipment details
            $table->enum('availability_status', ['available', 'occupied', 'maintenance', 'reserved', 'unavailable'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->json('accessibility_features')->nullable(); // ['ramp', 'elevator', 'wheelchair_accessible', etc]
            $table->enum('maintenance_status', ['good', 'needs_attention', 'under_maintenance', 'critical'])->default('good');
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->string('responsible_person', 255)->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->json('rules_and_regulations')->nullable();
            $table->json('usage_policies')->nullable();
            $table->json('schedule_rules')->nullable(); // Operating hours, booking rules, etc
            $table->string('photo')->nullable();
            $table->string('qr_code', 255)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['room_code']);
            $table->index(['building', 'floor']);
            $table->index(['room_type']);
            $table->index(['capacity']);
            $table->index(['availability_status']);
            $table->index(['is_active']);
            $table->index(['faculty']);
            $table->index(['department']);
            $table->index(['maintenance_status']);
            $table->index(['name']);
            $table->index(['building', 'room_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
