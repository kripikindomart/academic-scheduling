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
        Schema::create('whats_app_messages', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->string('message_id')->unique();
            $table->string('from_number');
            $table->string('to_number');
            $table->string('message_type')->default('text');
            $table->json('content')->nullable();
            $table->string('media_url')->nullable();
            $table->string('media_type')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('session_id')->references('session_id')->on('whats_app_sessions')->onDelete('cascade');
            $table->index('session_id');
            $table->index('message_id');
            $table->index('from_number');
            $table->index('to_number');
            $table->index('status');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whats_app_messages');
    }
};
