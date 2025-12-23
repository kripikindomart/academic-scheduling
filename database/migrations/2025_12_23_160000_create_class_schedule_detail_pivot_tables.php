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
        // Create pivot table for class_schedule_details and lecturers (many-to-many)
        Schema::create('class_schedule_detail_lecturer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_schedule_detail_id')->constrained()->onDelete('cascade');
            $table->foreignId('lecturer_id')->constrained()->onDelete('cascade');
            $table->boolean('is_primary')->default(false); // Primary lecturer flag
            $table->timestamps();

            $table->unique(['class_schedule_detail_id', 'lecturer_id'], 'csd_lecturer_unique');
        });

        // Create pivot table for class_schedule_details and rooms (many-to-many)
        Schema::create('class_schedule_detail_room', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_schedule_detail_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->boolean('is_primary')->default(false); // Primary room flag
            $table->timestamps();

            $table->unique(['class_schedule_detail_id', 'room_id'], 'csd_room_unique');
        });

        // Create pivot table for schedules and lecturers (many-to-many)
        Schema::create('schedule_lecturer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('lecturer_id')->constrained()->onDelete('cascade');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['schedule_id', 'lecturer_id']);
        });

        // Create pivot table for schedules and rooms (many-to-many)
        Schema::create('schedule_room', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['schedule_id', 'room_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_room');
        Schema::dropIfExists('schedule_lecturer');
        Schema::dropIfExists('class_schedule_detail_room');
        Schema::dropIfExists('class_schedule_detail_lecturer');
    }
};
