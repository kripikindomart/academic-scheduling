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
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();

            // Class identification
            $table->string('class_name', 100); // e.g., "3A", "2B", "4C"
            $table->string('class_code', 50)->unique(); // e.g., "TI-3A-2023"

            // Academic details
            $table->foreignId('program_study_id')->constrained()->onDelete('cascade');
            $table->string('academic_year', 20); // e.g., "2023/2024"
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
            $table->integer('year_level'); // 1, 2, 3, 4

            // Class information
            $table->integer('capacity')->default(40);
            $table->integer('current_students')->default(0);
            $table->foreignId('academic_advisor_id')->nullable()->constrained('lecturers')->onDelete('set null');

            // Class status
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            // Audit
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();

            // Indexes
            $table->index(['program_study_id', 'academic_year', 'semester']);
            $table->index(['class_code']);
            $table->index(['is_active']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
