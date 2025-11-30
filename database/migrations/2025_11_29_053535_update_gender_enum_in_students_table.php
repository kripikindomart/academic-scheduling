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
        Schema::table('students', function (Blueprint $table) {
            // Change gender enum from ['male', 'female', 'other'] to ['L', 'P']
            $table->enum('gender', ['L', 'P'])->default('L')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Revert back to ['male', 'female', 'other']
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->change();
        });
    }
};