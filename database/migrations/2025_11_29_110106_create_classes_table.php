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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Nama kelas (contoh: TI-1A, TI-1B, TI-1C)
            $table->string('code', 50)->unique(); // Kode unik kelas (contoh: TI-2025-GANJIL-A)
            $table->foreignId('program_study_id')->constrained()->onDelete('cascade');
            $table->integer('batch_year'); // Angkatan mahasiswa (contoh: 2025)
            $table->enum('semester', ['ganjil', 'genap']); // Semester
            $table->string('academic_year', 20); // Tahun ajaran (contoh: 2025/2026)
            $table->integer('capacity')->default(40); // Kapasitas maksimal mahasiswa
            $table->integer('current_students')->default(0); // Jumlah mahasiswa saat ini
            $table->string('room_number', 50)->nullable(); // Nomor ruangan kelas
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true); // Status kelas aktif/tidak
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['program_study_id', 'batch_year', 'semester']);
            $table->index('batch_year');
            $table->index('academic_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
