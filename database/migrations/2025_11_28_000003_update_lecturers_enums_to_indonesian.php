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
        // Handle existing data that doesn't match new enum values
        DB::statement("UPDATE lecturers SET gender = 'L' WHERE gender IN ('male', 'other')");
        DB::statement("UPDATE lecturers SET gender = 'P' WHERE gender = 'female'");

        DB::statement("UPDATE lecturers SET status = 'Aktif' WHERE status IN ('active', 'terminated', 'retired')");
        DB::statement("UPDATE lecturers SET status = 'Cuti' WHERE status = 'on_leave'");
        DB::statement("UPDATE lecturers SET status = 'Tidak' WHERE status = 'inactive'");

        DB::statement("UPDATE lecturers SET employment_type = 'Tetap' WHERE employment_type = 'permanent'");
        DB::statement("UPDATE lecturers SET employment_type = 'Kontrak' WHERE employment_type = 'contract'");
        DB::statement("UPDATE lecturers SET employment_type = 'Paruh' WHERE employment_type = 'part_time'");
        DB::statement("UPDATE lecturers SET employment_type = 'Tamu' WHERE employment_type = 'guest'");

        Schema::table('lecturers', function (Blueprint $table) {
            // Update gender enum to Indonesian abbreviations
            $table->enum('gender', ['L', 'P'])->default('L')->change();

            // Update status enum to Indonesian values
            $table->enum('status', ['Aktif', 'Cuti', 'Tidak'])->default('Aktif')->change();

            // Update employment_type enum to Indonesian values (shorter)
            $table->enum('employment_type', ['Tetap', 'Kontrak', 'Paruh', 'Tamu'])->default('Tetap')->change();

            // highest_education remains the same (S1, S2, S3)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert existing data
        DB::statement("UPDATE lecturers SET gender = 'male' WHERE gender = 'L'");
        DB::statement("UPDATE lecturers SET gender = 'female' WHERE gender = 'P'");

        DB::statement("UPDATE lecturers SET status = 'active' WHERE status = 'Aktif'");
        DB::statement("UPDATE lecturers SET status = 'on_leave' WHERE status = 'Cuti'");
        DB::statement("UPDATE lecturers SET status = 'inactive' WHERE status = 'Tidak'");

        DB::statement("UPDATE lecturers SET employment_type = 'permanent' WHERE employment_type = 'Tetap'");
        DB::statement("UPDATE lecturers SET employment_type = 'contract' WHERE employment_type = 'Kontrak'");
        DB::statement("UPDATE lecturers SET employment_type = 'part_time' WHERE employment_type = 'Paruh'");
        DB::statement("UPDATE lecturers SET employment_type = 'guest' WHERE employment_type = 'Tamu'");

        Schema::table('lecturers', function (Blueprint $table) {
            // Revert to English values
            $table->enum('gender', ['male', 'female', 'other'])->default('male')->change();

            // Revert status to English values
            $table->enum('status', ['active', 'inactive', 'on_leave', 'terminated', 'retired'])->default('active')->change();

            // Revert employment_type to English values
            $table->enum('employment_type', ['permanent', 'contract', 'part_time', 'guest'])->default('permanent')->change();

            // highest_education remains the same
        });
    }
};