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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code', 20)->unique();
            $table->string('course_name', 255);
            $table->text('description')->nullable();
            $table->integer('credits')->default(3);
            $table->string('semester', 20);
            $table->string('academic_year', 10);
            $table->enum('course_type', ['mandatory', 'elective'])->default('mandatory');
            $table->enum('level', ['undergraduate', 'graduate', 'doctoral'])->default('undergraduate');
            $table->integer('capacity')->default(50);
            $table->integer('current_enrollment')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('program_study_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['course_code', 'academic_year']);
            $table->index(['program_study_id', 'semester']);
            $table->index(['course_type', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
