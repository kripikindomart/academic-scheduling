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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number', 20)->unique();
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
            $table->enum('status', ['active', 'inactive', 'graduated', 'dropped_out', 'on_leave'])->default('active');
            $table->date('enrollment_date');
            $table->date('graduation_date')->nullable();
            $table->integer('current_semester')->default(1);
            $table->integer('current_year')->default(1);
            $table->decimal('gpa', 4, 2)->default(0.00);
            $table->string('class', 50);
            $table->string('batch_year', 4);
            $table->boolean('is_regular')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('father_name', 255)->nullable();
            $table->string('mother_name', 255)->nullable();
            $table->string('parent_phone', 50)->nullable();
            $table->string('parent_email', 255)->nullable();
            $table->text('parent_address')->nullable();
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('program_study_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_number']);
            $table->index(['program_study_id', 'status']);
            $table->index(['status']);
            $table->index(['batch_year']);
            $table->index(['is_active']);
            $table->index(['name']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
