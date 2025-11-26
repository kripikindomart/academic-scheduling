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
        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number', 20)->unique();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->string('phone', 50)->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('birth_date');
            $table->string('birth_place', 255);
            $table->text('address');
            $table->string('city', 255);
            $table->string('province', 255);
            $table->string('postal_code', 20);
            $table->string('nationality', 100)->default('Indonesia');
            $table->string('religion', 100);
            $table->string('blood_type', 10)->nullable();
            $table->string('id_card_number', 50)->unique();
            $table->string('passport_number', 50)->nullable()->unique();
            $table->enum('status', ['active', 'inactive', 'on_leave', 'terminated', 'retired'])->default('active');
            $table->string('employment_status', 100);
            $table->enum('employment_type', ['permanent', 'contract', 'part_time', 'guest'])->default('permanent');
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->string('position', 255);
            $table->string('rank', 100)->nullable(); // Prof, Dr, etc.
            $table->text('specialization')->nullable();
            $table->string('department', 255);
            $table->string('faculty', 255);
            $table->enum('highest_education', ['S1', 'S2', 'S3'])->nullable();
            $table->string('education_institution', 255)->nullable();
            $table->string('education_major', 255)->nullable();
            $table->integer('graduation_year')->nullable();
            $table->json('certifications')->nullable();
            $table->json('research_interests')->nullable();
            $table->json('publications')->nullable();
            $table->integer('academic_load')->default(12); // Maximum credit hours
            $table->string('office_room', 100)->nullable();
            $table->json('office_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('program_study_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_number']);
            $table->index(['program_study_id', 'status']);
            $table->index(['status']);
            $table->index(['employment_type']);
            $table->index(['faculty']);
            $table->index(['department']);
            $table->index(['is_active']);
            $table->index(['name']);
            $table->index(['email']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturers');
    }
};
