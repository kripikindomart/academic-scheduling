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
        Schema::table('class_schedules', function (Blueprint $table) {
            // Drop existing foreign key constraint
            $table->dropForeign(['class_id']);

            // Add new foreign key constraint pointing to classes table
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            // Drop the current foreign key
            $table->dropForeign(['class_id']);

            // Restore the original foreign key constraint
            $table->foreign('class_id')->references('id')->on('school_classes')->onDelete('cascade');
        });
    }
};
