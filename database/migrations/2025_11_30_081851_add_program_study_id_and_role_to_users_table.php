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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('program_study_id')->nullable()->after('email');
            $table->string('role')->default('user')->after('program_study_id');
            $table->foreign('program_study_id')->references('id')->on('program_studies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['program_study_id']);
            $table->dropColumn(['program_study_id', 'role']);
        });
    }
};
