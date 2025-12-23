<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration removes the redundant lecturer_id and room_id columns
     * from class_schedule_details and schedules tables since we now use
     * pivot tables for many-to-many relationships.
     */
    public function up(): void
    {
        // First, migrate existing data to pivot tables if not already migrated
        $this->migrateExistingDataToPivot();

        // Drop the columns from class_schedule_details
        Schema::table('class_schedule_details', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['lecturer_id']);
            $table->dropForeign(['room_id']);
            
            // Drop the columns
            $table->dropColumn(['lecturer_id', 'room_id']);
        });

        // Drop the columns from schedules
        Schema::table('schedules', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['lecturer_id']);
            $table->dropForeign(['room_id']);
            
            // Drop the columns
            $table->dropColumn(['lecturer_id', 'room_id']);
        });
    }

    /**
     * Migrate existing data from single FK columns to pivot tables.
     */
    private function migrateExistingDataToPivot(): void
    {
        // Migrate class_schedule_details data
        $details = DB::table('class_schedule_details')
            ->whereNotNull('lecturer_id')
            ->orWhereNotNull('room_id')
            ->get();

        foreach ($details as $detail) {
            // Migrate lecturer if exists and not already in pivot
            if ($detail->lecturer_id) {
                $exists = DB::table('class_schedule_detail_lecturer')
                    ->where('class_schedule_detail_id', $detail->id)
                    ->where('lecturer_id', $detail->lecturer_id)
                    ->exists();
                
                if (!$exists) {
                    DB::table('class_schedule_detail_lecturer')->insert([
                        'class_schedule_detail_id' => $detail->id,
                        'lecturer_id' => $detail->lecturer_id,
                        'is_primary' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Migrate room if exists and not already in pivot
            if ($detail->room_id) {
                $exists = DB::table('class_schedule_detail_room')
                    ->where('class_schedule_detail_id', $detail->id)
                    ->where('room_id', $detail->room_id)
                    ->exists();
                
                if (!$exists) {
                    DB::table('class_schedule_detail_room')->insert([
                        'class_schedule_detail_id' => $detail->id,
                        'room_id' => $detail->room_id,
                        'is_primary' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Migrate schedules data
        $schedules = DB::table('schedules')
            ->whereNotNull('lecturer_id')
            ->orWhereNotNull('room_id')
            ->get();

        foreach ($schedules as $schedule) {
            // Migrate lecturer if exists and not already in pivot
            if ($schedule->lecturer_id) {
                $exists = DB::table('schedule_lecturer')
                    ->where('schedule_id', $schedule->id)
                    ->where('lecturer_id', $schedule->lecturer_id)
                    ->exists();
                
                if (!$exists) {
                    DB::table('schedule_lecturer')->insert([
                        'schedule_id' => $schedule->id,
                        'lecturer_id' => $schedule->lecturer_id,
                        'is_primary' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Migrate room if exists and not already in pivot
            if ($schedule->room_id) {
                $exists = DB::table('schedule_room')
                    ->where('schedule_id', $schedule->id)
                    ->where('room_id', $schedule->room_id)
                    ->exists();
                
                if (!$exists) {
                    DB::table('schedule_room')->insert([
                        'schedule_id' => $schedule->id,
                        'room_id' => $schedule->room_id,
                        'is_primary' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add columns to class_schedule_details
        Schema::table('class_schedule_details', function (Blueprint $table) {
            $table->foreignId('lecturer_id')->nullable()->after('course_id')->constrained()->onDelete('set null');
            $table->foreignId('room_id')->nullable()->after('lecturer_id')->constrained()->onDelete('set null');
        });

        // Re-add columns to schedules
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('lecturer_id')->nullable()->after('course_id')->constrained('lecturers')->onDelete('set null');
            $table->foreignId('room_id')->nullable()->after('lecturer_id')->constrained()->onDelete('set null');
        });

        // Migrate data back from pivot tables
        $this->migrateDataBackFromPivot();
    }

    /**
     * Migrate data back from pivot tables to single FK columns.
     */
    private function migrateDataBackFromPivot(): void
    {
        // Migrate from class_schedule_detail_lecturer
        $lecturerPivots = DB::table('class_schedule_detail_lecturer')
            ->where('is_primary', true)
            ->get();
        
        foreach ($lecturerPivots as $pivot) {
            DB::table('class_schedule_details')
                ->where('id', $pivot->class_schedule_detail_id)
                ->update(['lecturer_id' => $pivot->lecturer_id]);
        }

        // Migrate from class_schedule_detail_room
        $roomPivots = DB::table('class_schedule_detail_room')
            ->where('is_primary', true)
            ->get();
        
        foreach ($roomPivots as $pivot) {
            DB::table('class_schedule_details')
                ->where('id', $pivot->class_schedule_detail_id)
                ->update(['room_id' => $pivot->room_id]);
        }

        // Migrate from schedule_lecturer
        $scheduleLecturerPivots = DB::table('schedule_lecturer')
            ->where('is_primary', true)
            ->get();
        
        foreach ($scheduleLecturerPivots as $pivot) {
            DB::table('schedules')
                ->where('id', $pivot->schedule_id)
                ->update(['lecturer_id' => $pivot->lecturer_id]);
        }

        // Migrate from schedule_room
        $scheduleRoomPivots = DB::table('schedule_room')
            ->where('is_primary', true)
            ->get();
        
        foreach ($scheduleRoomPivots as $pivot) {
            DB::table('schedules')
                ->where('id', $pivot->schedule_id)
                ->update(['room_id' => $pivot->room_id]);
        }
    }
};
