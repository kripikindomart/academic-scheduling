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
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('schedule_code')->unique();
            $table->string('title');
            $table->foreignId('program_study_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('academic_year_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('semester');
            $table->decimal('online_percentage', 5, 2)->default(0);
            $table->decimal('offline_percentage', 5, 2)->default(100);
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft, active, completed, cancelled
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_from_ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['program_study_id', 'class_id', 'academic_year_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};