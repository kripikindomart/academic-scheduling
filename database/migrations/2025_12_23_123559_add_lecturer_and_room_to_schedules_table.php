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
        Schema::table('schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('schedules', 'lecturer_id')) {
                $table->foreignId('lecturer_id')->nullable()->constrained('lecturers')->onDelete('set null')->after('course_id');
            }
            if (!Schema::hasColumn('schedules', 'room_id')) {
                $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('set null')->after('lecturer_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (Schema::hasColumn('schedules', 'lecturer_id')) {
                $table->dropForeign(['lecturer_id']);
                $table->dropColumn('lecturer_id');
            }
            if (Schema::hasColumn('schedules', 'room_id')) {
                $table->dropForeign(['room_id']);
                $table->dropColumn('room_id');
            }
        });
    }
};
