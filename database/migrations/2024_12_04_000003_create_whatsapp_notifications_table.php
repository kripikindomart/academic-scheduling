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
        Schema::create('whatsapp_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->string('notification_type');
            $table->string('recipient_number');
            $table->string('recipient_name')->nullable();
            $table->text('message_content');
            $table->string('template_name')->nullable();
            $table->json('template_data')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('last_retry_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('session_id')->references('session_id')->on('whatsapp_sessions')->onDelete('cascade');
            $table->index('session_id');
            $table->index('notification_type');
            $table->index('recipient_number');
            $table->index('status');
            $table->index('scheduled_at');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_notifications');
    }
};