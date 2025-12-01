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
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('class_schedule_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('class_schedule_detail_id')->nullable()->after('class_schedule_id')->constrained()->onDelete('cascade');
            $table->index(['class_schedule_id']);
            $table->index(['class_schedule_detail_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['class_schedule_detail_id']);
            $table->dropForeign(['class_schedule_id']);
            $table->dropIndex(['class_schedule_detail_id']);
            $table->dropIndex(['class_schedule_id']);
            $table->dropColumn(['class_schedule_detail_id', 'class_schedule_id']);
        });
    }
};