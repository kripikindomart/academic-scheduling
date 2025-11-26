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
        Schema::create('program_studies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('faculty', 255);
            $table->enum('level', ['undergraduate', 'graduate', 'doctoral'])->default('undergraduate');
            $table->enum('degree', ['S1', 'S2', 'S3', 'D3', 'D4'])->default('S1');
            $table->integer('duration_years')->default(4);
            $table->integer('minimum_credits')->default(144);
            $table->string('head_of_program', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('office_location', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['code']);
            $table->index(['faculty', 'level']);
            $table->index(['level', 'degree']);
            $table->index(['is_active']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_studies');
    }
};
