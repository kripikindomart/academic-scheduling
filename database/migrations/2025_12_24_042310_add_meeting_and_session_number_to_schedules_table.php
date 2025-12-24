<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Meeting number - Pertemuan ke-1, 2, 3, dst
            $table->integer('meeting_number')->nullable()->after('week_number');
            
            // Session number within meeting - Sesi ke-1, 2 dalam pertemuan
            $table->integer('session_number')->nullable()->after('meeting_number');
            
            // Total sessions in this meeting
            $table->integer('total_sessions')->nullable()->after('session_number');
        });

        // Update session_type enum to include kuliah, uts, uas
        // Need to modify the enum in a separate statement
        DB::statement("ALTER TABLE schedules MODIFY COLUMN session_type VARCHAR(50) DEFAULT 'kuliah'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['meeting_number', 'session_number', 'total_sessions']);
        });

        // Revert session_type to original enum
        DB::statement("ALTER TABLE schedules MODIFY COLUMN session_type ENUM('lecture', 'lab', 'seminar', 'tutorial', 'exam', 'meeting') DEFAULT 'lecture'");
    }
};
