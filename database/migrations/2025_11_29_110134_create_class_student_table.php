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
        Schema::create('class_student', function (Blueprint $table) {
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('enrollment_date'); // Tanggal mahasiswa enroll ke kelas
            $table->enum('status', ['active', 'inactive', 'transferred', 'dropped'])->default('active');
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Unique combination to prevent duplicate enrollment
            $table->unique(['class_id', 'student_id']);

            // Indexes for performance
            $table->index('student_id');
            $table->index('class_id');
            $table->index('status');
            $table->index('enrollment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_student');
    }
};
