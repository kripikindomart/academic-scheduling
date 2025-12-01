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
        Schema::create('class_schedule_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lecturer_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->string('day_of_week'); // monday, tuesday, wednesday, thursday, friday, saturday, sunday
            $table->time('start_time');
            $table->time('end_time');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('sessions_per_meeting')->default(1); // 1 atau 2 sesi per pertemuan
            $table->integer('total_meetings')->default(0);
            $table->string('meeting_type')->default('lecture'); // lecture, lab, seminar, workshop, online
            $table->boolean('is_online')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['class_schedule_id', 'day_of_week']);
            $table->index(['course_id', 'lecturer_id']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_schedule_details');
    }
};